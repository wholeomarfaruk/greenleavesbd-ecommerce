<?php

namespace App\Http\Controllers;
use ShahariarAhmad\CourierFraudCheckerBd\Services\PathaoService;
use ShahariarAhmad\CourierFraudCheckerBd\Services\SteadfastService;
use App\Exports\OrderExport;
use App\Models\Analytic;
use App\Models\Size;
use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\products;
use App\Models\delivery_areas;
use App\Models\Order;
use App\Models\Order_Item;
use App\Models\User;
use App\Models\Slide;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use ShahariarAhmad\CourierFraudCheckerBd\Facade\CourierFraudCheckerBd;

class AdminController extends Controller
{
    public function index()
    {
        $ordersQuery = Order::query()->where('status', '!=', 'autosave');

        $pending_orders = (clone $ordersQuery)->where('status', 'pending')->count();
        $pending_orders_sum = (clone $ordersQuery)->where('status', 'pending')->sum('total');

        $delivered_orders = (clone $ordersQuery)->where('status', 'delivered')->count();
        $delivered_orders_sum = (clone $ordersQuery)->where('status', 'delivered')->sum('total');

        $cancelled_orders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $cancelled_orders_sum = (clone $ordersQuery)->where('status', 'cancelled')->sum('total');

        $total_orders = $this->activeOrdersQuery()->count();
        $total_orders_sum = $this->activeOrdersQuery()->sum('total');

        $orders = $this->activeOrdersQuery()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.index', compact('pending_orders', 'delivered_orders', 'cancelled_orders', 'total_orders', 'pending_orders_sum', 'delivered_orders_sum', 'cancelled_orders_sum', 'total_orders_sum', 'orders'));
    }
    public function login()
    {
        return view('admin.login');
    }


    //Products
    public function products(Request $request)
    {
        $search = $request->search;
        if ($search) {
            $products = products::where('name', 'like', '%' . $search . '%')
            ->orWhere('id', 'like', '%' . $search . '%')
            ->orWhere('price', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        }else{

            $products = products::orderBy('created_at', 'desc')->paginate(20);
        }
        return view('admin.products', compact('products'));
    }

    public function productsAdd()
    {
        return view('admin.products-add');
    }
    public function productStore(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'quantity' => 'required|integer',
            'image' => 'mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        try {
            //code...

            $product = new products();

            $product->name = $request->name;

            $product->price = $request->price;
            if ($request->discount_price) {
                $product->discount_price = $request->discount_price;
            }
            if ($request->name) {
                $slug = Str::slug($request->name);
                if (products::where('slug', $slug)->exists()) {
                    $slug = $slug . '-' . Carbon::now()->timestamp;
                }
                $product->slug = $slug;
            }
            $product->featured = $request->featured ? true : false;

            if ($request->sku) {
                $product->sku = $request->sku;
            }
            $product->stock_status = $request->stock_status;

            $product->quantity = $request->quantity;



            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $filename = Carbon::now()->timestamp . "." . $extension;
                $this->generateProductThumbnailImage($image, $filename);
                $product->image = $filename;
            }

            if ($request->description) {
                $product->description = $request->description;
            }
            if ($request->short_description) {
                $product->short_description = $request->short_description;
            }
            if ($request->has('status')) {
                $product->status = $request->status ? true : false;
            }

            $product->save();

            if ($request->has('sizes')) {

                foreach ($request->sizes as $key => $size) {
                    $size = Size::create([
                        'products_id' => $product->id,
                        'name' => $size
                    ]);
                }
            }
            if ($request->hasFile('images')) {

                // Store file in 'public/media'
                $images = $request->file('images');
                $path = 'storage/images/products/' . $product->id . '/';
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0777, true);
                }
                foreach ($images as $key => $file) {

                    // Save in media table

                    $media = new Media();
                    $media->filename = basename($file->getClientOriginalName());
                    $media->original_name = $file->getClientOriginalName();
                    $media->mime_type = $file->getMimeType();
                    $media->extension = $file->getClientOriginalExtension();
                    $media->size = $file->getSize();
                    $media->type = 'image';
                    $media->category = 'product_images';
                    $media->disk = 'public';
                    $media->path = $path . $file->getClientOriginalName();
                    $media->mediable_id = $product->id;
                    $media->mediable_type = products::class;
                    if ($request->has('caption')) {
                        $media->caption = $request->input('caption');
                    }

                    $media->user_id = auth()->id();
                    $media->save();
                    $file->move(public_path($path), $file->getClientOriginalName());

                }


            }
            if ($request->hasFile('sizechart')) {

                // Store file in 'public/media'
                $images = $request->file('images');
                $path = 'storage/images/products/' . $product->id . '/';
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0777, true);
                }

                    // Save in media table
                    $media = new Media();
                    $media->filename = basename($file->getClientOriginalName());
                    $media->original_name = $file->getClientOriginalName();
                    $media->mime_type = $file->getMimeType();
                    $media->extension = $file->getClientOriginalExtension();
                    $media->size = $file->getSize();
                    $media->type = 'image';
                    $media->category = 'sizechart';
                    $media->disk = 'public';
                    $media->path = $path . $file->getClientOriginalName();
                    $media->mediable_id = $product->id;
                    $media->mediable_type = products::class;
                    if ($request->has('caption')) {
                        $media->caption = $request->input('caption');
                    }

                    $media->user_id = auth()->id();
                    $media->save();
                    $file->move(public_path($path), $file->getClientOriginalName());



            }
            if ($request->has('categories')) {
                $product->categories()->attach($request->categories);

            }
            // if ($request->has('segments')) {
            //     $product->segments()->attach($request->segments);
            // }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());

        }
        return redirect()->route('admin.products')->with('toast',['status' => 'success', 'message' => 'Product Updated Successfully']);
    }

    public function generateProductThumbnailImage($image, $imageName)
    {
        $thumbnail_path = public_path('storage/images/products/thumbnails/');
        $image_path = public_path('storage/images/products/');

        if(!file_exists($thumbnail_path)) {
            mkdir($thumbnail_path, 0777, true);
        }
        if(!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }
        $image = Image::read($image->path());
        $image->save($image_path . $imageName, 70);
        $image->save($thumbnail_path . $imageName, 70);

    }

    public function productEdit($id)
    {
        $product = products::find($id);
        return view('admin.products-edit', compact('product', ));
    }
    public function productUpdate(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'featured' => 'boolean',
            'quantity' => 'required|integer',
            'image' => 'mimes:jpg,jpeg,png,webp|max:2048',

        ]);
        $product = products::find($request->id);
        $product->name = $request->name;
        $product->price = $request->price;
        if ($request->slug) {
            $slug = $request->slug;
            if (products::where('slug', $slug)->whereNotIn('id', [$product->id])->exists()) {
                $slug = $slug . '-' . Carbon::now()->timestamp;
            }
            $product->slug = $slug;
        }


        $product->discount_price = $request->discount_price;

        if ($request->sku) {
            $product->sku = $request->sku;
        }
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ? true : false;
        $product->quantity = $request->quantity;
        if ($request->description) {
            $product->description = $request->description;
        }
        if ($request->hasFile('image')) {
            if (File::exists(public_path('storage/images/products/thumbnails/' . $product->image))) {
                File::delete(public_path('storage/images/products/thumbnails/' . $product->image));
                File::delete(public_path('storage/images/products/' . $product->image));
            }
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = Carbon::now()->timestamp . "." . $extension;
            $this->generateProductThumbnailImage($image, $filename);
            $product->image = $filename;
        }


        if ($request->short_description) {
            $product->short_description = $request->short_description;
        }
        if ($request->has('status')) {
            $product->status = true;
        } else {
            $product->status = false;
        }

        $product->save();
        $product->sizes()->delete();
        if ($request->has('sizes')) {

            foreach ($request->sizes as $key => $size) {
                $size = Size::create([
                    'products_id' => $product->id,
                    'name' => $size
                ]);
            }
        }
        if ($request->hasFile('images')) {

            // Store file in 'public/media'
            $images = $request->file('images');
            $path = 'storage/images/products/' . $product->id . '/';
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0777, true);
            }
            $old_media = $product->media()->where('category', 'product_images')->get();
            foreach ($old_media as $media) {
                if (file_exists(public_path($media->path))) {
                    unlink(public_path($media->path));
                }
                $media->delete();
            }

            foreach ($images as $key => $file) {


                // Save in media table
                $media = new Media();
                $media->filename = basename($file->getClientOriginalName());
                $media->original_name = $file->getClientOriginalName();
                $media->mime_type = $file->getMimeType();
                $media->extension = $file->getClientOriginalExtension();
                $media->size = $file->getSize();
                $media->type = 'image';
                $media->category = 'product_images';
                $media->disk = 'public';
                $media->path = $path . $file->getClientOriginalName();
                $media->mediable_id = $product->id;
                $media->mediable_type = products::class;
                if ($request->has('caption')) {
                    $media->caption = $request->input('caption');
                }

                $media->user_id = auth()->id();
                $media->save();
                $file->move(public_path($path), $file->getClientOriginalName());

            }


        }
        if ($request->hasFile('sizechart')) {

            // Store file in 'public/media'
            $images = $request->file('sizechart');
            $path = 'storage/images/products/' . $product->id . '/';
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0777, true);
            }
            $old_media = $product->sizeChart;
            if ($old_media) {
                $media = $old_media;

                if (file_exists(public_path($media->path))) {
                    unlink(public_path($media->path));
                }
                $media->delete();
            }


                $file = $images;
                // Save in media table
                $media = new Media();
                $media->filename = basename($file->getClientOriginalName());
                $media->original_name = $file->getClientOriginalName();
                $media->mime_type = $file->getMimeType();
                $media->extension = $file->getClientOriginalExtension();
                $media->size = $file->getSize();
                $media->type = 'image';
                $media->category = 'sizechart';
                $media->disk = 'public';
                $media->path = $path . $file->getClientOriginalName();
                $media->mediable_id = $product->id;
                $media->mediable_type = products::class;
                if ($request->has('caption')) {
                    $media->caption = $request->input('caption');
                }

                $media->user_id = auth()->id();
                $media->save();
                $file->move(public_path($path), $file->getClientOriginalName());
        }
        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }
        if ($request->has('segments')) {
            $product->segments()->sync($request->segments);
        }

        return redirect()->route('admin.products')->with('status', 'Product Updated Successfully');
    }


    public function productDelete($id)
    {
        $product = products::find($id);
        if (File::exists(public_path('storage/images/products/thumbnails/' . $product->image))) {
            File::delete(public_path('storage/images/products/thumbnails/' . $product->image));
            File::delete(public_path('storage/images/products/' . $product->image));

        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product Deleted Successfully');
    }
    public function coupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.coupons', compact('coupons'));
    }
    public function couponAdd()
    {
        return view('admin.coupons-add');
    }
    public function couponStore(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date_format:Y-m-d',
        ]);
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Added Successfully');
    }
    public function couponEdit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupons-edit', compact('coupon'));
    }
    public function couponUpdate(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date_format:Y-m-d',
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Updated Successfully');
    }
    public function couponDelete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Deleted Successfully');
    }
    protected function availableOrderStatuses(): array
    {
        return [
            'pending',
            'confirmed',
            'processing',
            'ready',
            'in_review',
            'in_transit',
            'delivered',
            'delivery_in_review',
            'on_hold',
            'cancelled',
            'returned',
            'deleted',
        ];
    }

    protected function activeOrdersQuery(): Builder
    {
        return Order::query()->where(function (Builder $builder): void {
            $builder->whereNull('status')
                ->orWhereNotIn('status', ['deleted', 'autosave']);
        });
    }

    protected function orderStatusGroups()
    {
        return Order::query()
            ->whereNotNull('status')
            ->whereNotIn('status', ['deleted', 'autosave'])
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    protected function buildOrderQuery(Request $request, ?string $fixedStatus = null): Builder
    {
        $search = trim((string) $request->input('search'));
        $status = $fixedStatus ?: trim((string) $request->input('order_status'));

        $query = Order::query()
            ->with(['items.product', 'customer', 'delivery_area', 'device'])
            ->withCount('items')
            ->orderByDesc('created_at');

        if ($fixedStatus === 'deleted') {
            $query->where('status', 'deleted');
        } else {
            $query->where(function (Builder $builder): void {
                $builder->whereNull('status')
                    ->orWhereNotIn('status', ['deleted', 'autosave']);
            });

            if ($status !== '' && in_array($status, $this->availableOrderStatuses(), true) && $status !== 'deleted') {
                $query->where('status', $status);
            }
        }

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('id', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('full_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('payment_method', 'like', '%' . $search . '%')
                    ->orWhere('payment_status', 'like', '%' . $search . '%')
                    ->orWhere('consignment_id', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhereHas('items.product', function (Builder $productQuery) use ($search): void {
                        $productQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query;
    }

    protected function renderOrdersPage(Request $request, ?string $fixedStatus = null, string $pageTitle = 'Orders')
    {
        $orders = $this->buildOrderQuery($request, $fixedStatus)
            ->paginate(20)
            ->withQueryString();

        $status_group = $this->orderStatusGroups();
        $orders_count = $this->activeOrdersQuery()->count();
        $deleted_orders_count = Order::query()->where('status', 'deleted')->count();
        $activeStatus = $fixedStatus ?: trim((string) $request->input('order_status'));
        $search = trim((string) $request->input('search'));

        return view('admin.orders.index', compact(
            'orders',
            'status_group',
            'orders_count',
            'deleted_orders_count',
            'activeStatus',
            'search',
            'pageTitle',
        ));
    }

    protected function applyOrderStatus(Order $order, string $status): void
    {
        $order->status = $status;

        if ($status === 'delivered' && empty($order->delivery_date)) {
            $order->delivery_date = Carbon::now();
        }

        if ($status === 'delivered' && ($order->payment_method ?? 'cod') === 'cod' && $order->payment_status !== 'paid') {
            $order->payment_status = 'paid';
        }

        if ($status === 'cancelled' && empty($order->cancelled_date)) {
            $order->cancelled_date = Carbon::now();
        }

        $order->save();
    }

    public function orders(Request $request)
    {
        return $this->renderOrdersPage($request, null, 'All Orders');
    }

    public function ordersPending(Request $request)
    {
        return $this->renderOrdersPage($request, 'pending', 'Pending Orders');
    }

    public function ordersConfirmed(Request $request)
    {
        return $this->renderOrdersPage($request, 'confirmed', 'Confirmed Orders');
    }

    public function ordersProcessing(Request $request)
    {
        return $this->renderOrdersPage($request, 'processing', 'Processing Orders');
    }

    public function ordersReady(Request $request)
    {
        return $this->renderOrdersPage($request, 'ready', 'Ready Orders');
    }

    public function ordersInReview(Request $request)
    {
        return $this->renderOrdersPage($request, 'in_review', 'In Review Orders');
    }

    public function ordersInTransit(Request $request)
    {
        return $this->renderOrdersPage($request, 'in_transit', 'In Transit Orders');
    }

    public function ordersDelivered(Request $request)
    {
        return $this->renderOrdersPage($request, 'delivered', 'Delivered Orders');
    }

    public function ordersDeliveryInReview(Request $request)
    {
        return $this->renderOrdersPage($request, 'delivery_in_review', 'Delivery In Review Orders');
    }

    public function ordersOnHold(Request $request)
    {
        return $this->renderOrdersPage($request, 'on_hold', 'On Hold Orders');
    }

    public function ordersCancelled(Request $request)
    {
        return $this->renderOrdersPage($request, 'cancelled', 'Cancelled Orders');
    }

    public function ordersReturned(Request $request)
    {
        return $this->renderOrdersPage($request, 'returned', 'Returned Orders');
    }

    public function deletedOrders(Request $request)
    {
        return $this->renderOrdersPage($request, 'deleted', 'Deleted Orders');
    }

    public function orderDetails($id)
    {
        $order = Order::query()
            ->with(['items.product', 'customer', 'delivery_area', 'device', 'user'])
            ->findOrFail($id);

        return view('admin.orders.show', [
            'order' => $order,
            'orderItems' => $order->items,
        ])->with('title', 'Order Details');
    }

    public function orderStatusUpdate(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:' . implode(',', $this->availableOrderStatuses()),
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->status === $validated['status']) {
            return redirect()->route('admin.orders.details', $order->id)->with('status', 'Order Status Already Updated');
        }

        $this->applyOrderStatus($order, $validated['status']);

        return redirect()->route('admin.orders.details', $order->id)->with('status', 'Order Status Updated Successfully');
    }

    public function ordersoftdelete($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }

        if ($order->status == 'deleted') {
            return redirect()->back()->with('status', 'Order Already Deleted');
        }

        $this->applyOrderStatus($order, 'deleted');

        return redirect()->back()->with('status', 'Order Deleted Successfully');
    }
    public function deleteOrder($id)
    {
        $order = Order::find($id);
        $orderItems = Order_Item::where('order_id', $id)->get();
        foreach ($orderItems as $orderItem) {
            $orderItem->delete();
        }
        $order->delete();
        return redirect()->back()->with('status', 'Order Deleted Successfully');
    }
    public function exportOrders(Request $request)
    {
        $order_status = $request->order_status ?? null;
        if ($request->has('order_status')) {

            $fileName = $order_status . '_orders_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $fileName = 'orders_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        }

        return Excel::download(new OrderExport($order_status), $fileName);
    }
    public function updateOrder(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }
        $order->Order_Item()->delete();
        // $order->Order_Item()
        $orderedProducts = products::whereIn('id', $request->products)->get();
        foreach ($orderedProducts as $orderedProduct) {
            $price = (float) ($orderedProduct->discount_price ?? $orderedProduct->price);
            $quantity = (int) ($request->order_items[$orderedProduct->id]['quantity'] ?? 1);

            $orderItem = new Order_Item();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $orderedProduct->id;
            $orderItem->product_name = $orderedProduct->name;
            $orderItem->product_image = $orderedProduct->image ? 'storage/images/products/thumbnails/' . $orderedProduct->image : null;
            $orderItem->quantity = $quantity;
            $orderItem->unit_price = $price;
            $orderItem->price = $price;
            $orderItem->line_total = round($price * $quantity, 2);
            $orderItem->options = json_encode(['size' => $request->order_items[$orderedProduct->id]['size']]);
            $orderItem->save();
        }
        $subtotal = $orderedProducts->sum(function ($product) use ($request) {
            return (float) ($product->discount_price ?? $product->price) * (float) $request->order_items[$product->id]['quantity'];
        });
        $order->subtotal = $subtotal;
        $order->discount = $request->discount;
        $order->fee = $request->delivery_charge;
        $order->total = ($subtotal + (float) $request->delivery_charge) - (float) $request->discount;

        $order->save();



        $order->save();

        // return $request->all();
        return redirect()->back()->with('status', 'Order Updated Successfully');
    }
    public function updateOrderDetails(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'city' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'payment_method' => 'nullable|in:cod,bkash',
            'payment_status' => 'nullable|in:unpaid,pending,paid,failed',
            'transaction_id' => 'nullable|string|max:255|required_if:payment_method,bkash',
        ]);

        $paymentMethod = $validated['payment_method'] ?? ($order->payment_method ?: 'cod');
        $paymentStatus = $validated['payment_status']
            ?? ($order->payment_status ?: ($paymentMethod === 'bkash' ? 'pending' : 'unpaid'));

        $order->name = $validated['name'];
        $order->full_name = $validated['name'];
        $order->phone = $validated['phone'];
        $order->email = $validated['email'] ?? null;
        $order->address = $validated['address'];
        $order->city = $validated['city'] ?? null;
        $order->area = $validated['area'] ?? null;
        $order->note = $validated['note'] ?? null;
        $order->payment_method = $paymentMethod;
        $order->payment_status = $paymentStatus;
        $order->transaction_id = $paymentMethod === 'bkash'
            ? ($validated['transaction_id'] ?? null)
            : null;
        $order->save();

        return redirect()->back()->with('status', 'Order Details Updated Successfully');
    }
    public function deliveryAreas()
    {
        $deliveryAreas = delivery_areas::orderBy('id', 'desc')->paginate(10);
        return view('admin.delivery-areas', compact('deliveryAreas'));
    }
    public function deliveryAreaAdd()
    {
        return view('admin.delivery-areas-add');
    }
    public function deliveryAreaStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'charge' => 'required|numeric',
        ]);
        $deliveryArea = new delivery_areas();
        $deliveryArea->name = $request->name;
        $deliveryArea->charge = $request->charge;
        $deliveryArea->save();
        return redirect()->route('admin.deliveryareas')->with('status', 'Delivery Area Added Successfully');
    }
    public function deliveryAreaEdit($id)
    {
        $deliveryArea = delivery_areas::find($id);
        return view('admin.delivery-areas-edit', compact('deliveryArea'));
    }
    public function deliveryAreaUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'charge' => 'required|numeric',
        ]);
        $deliveryArea = delivery_areas::find($request->id);
        $deliveryArea->name = $request->name;
        $deliveryArea->charge = $request->charge;
        $deliveryArea->save();
        return redirect()->route('admin.deliveryareas')->with('status', 'Delivery Area Updated Successfully');
    }

    public function deliveryAreaDelete($id)
    {
        $deliveryArea = delivery_areas::find($id);
        $deliveryArea->delete();
        return redirect()->route('admin.deliveryareas')->with('status', 'Delivery Area Deleted Successfully');
    }

    //Slides
    public function slides()
    {
        $slides = Slide::orderBy('id', 'desc')->paginate(10);
        return view('admin.slides', compact('slides'));
    }
    public function slideAdd()
    {
        return view('admin.slides-add');
    }
    public function slideStore(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'subtitle' => 'required',
            'tagline' => 'required',
            'image' => 'required|max:2048|mimes:jpg,jpeg,png,gif,webp',
        ]);

        $slide = new Slide();
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->tagline = $request->tagline;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = Carbon::now()->timestamp . "." . $extension;
            $this->GenerateSlideThumbnailImage($image, $filename);
            $slide->image = $filename;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide Added Successfully');
    }

    public function GenerateSlideThumbnailImage($image, $imageName)
    {
        $thumbnail_path = public_path('storage/images/slides/thumbnails/');
        $image_path = public_path('storage/images/slides/');
          if(!file_exists($thumbnail_path)) {
            mkdir($thumbnail_path, 0777, true);
        }
        if(!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }
        $image = Image::read($image->path());
        $originalWidth = $image->width();
        $originalHeight = $image->height();
        $image->save($image_path . $imageName);
        $image->resize($originalWidth, $originalHeight, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($image_path . $imageName);
        $image->cover(202, 202, 'top');
        $image->resize(202, 202, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($thumbnail_path . $imageName);
    }
    public function slideEdit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slides-edit', compact('slide'));
    }
    public function slideUpdate(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'subtitle' => 'required',
            'tagline' => 'required',
        ]);
        $slide = Slide::find($request->id);
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->tagline = $request->tagline;
        if ($request->hasFile('image')) {
            if (File::exists(public_path('storage/images/slides/thumbnails/' . $slide->image))) {
                File::delete(public_path('storage/images/slides/thumbnails/' . $slide->image));
                File::delete(public_path('storage/images/slides/' . $slide->image));
            }
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = Carbon::now()->timestamp . "." . $extension;
            $this->GenerateSlideThumbnailImage($image, $filename);
            $slide->image = $filename;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide Updated Successfully');
    }
    public function slideDelete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('storage/images/slides/thumbnails/' . $slide->image))) {
            File::delete(public_path('storage/images/slides/thumbnails/' . $slide->image));
            File::delete(public_path('storage/images/slides/' . $slide->image));
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('status', 'Slide Deleted Successfully');
    }
    //Analytics
    public function gAnalaytics()
    {
        $google_analytics = Analytic::where('slug', 'google-analytics')->first();
        return view('admin.google-analytics', compact('google_analytics'));
    }
    public function gAnalyticsUpdate(Request $request)
    {

        $google_analytics = Analytic::find($request->id);
        $google_analytics->code = $request->code ?? '';
        $google_analytics->save();
        return redirect()->route('admin.google.analytics')->with('status', 'Google Analytics Code Updated Successfully');
    }
    public function fbPixels()
    {
        $facebook_pixels = Analytic::where('slug', 'facebook-pixels')->first();
        return view('admin.facebook-pixels', compact('facebook_pixels'));
    }
    public function fbPixelsUpdate(Request $request)
    {
        $facebook_pixels = Analytic::find($request->id);
        $facebook_pixels->code = $request->code ?? '';
        $facebook_pixels->save();
        return redirect()->route('admin.facebook.pixels')->with('status', 'Facebook Pixels Code Updated Successfully');
    }






    public function analytics()
    {
        // Total stats
        $totalVisitors = Visit::distinct('ip_address')->count('ip_address');
        $totalPageViews = Visit::count();

        $todayVisitors = Visit::whereDate('created_at', today())
            ->distinct('ip_address')->count('ip_address');
        $todayPageViews = Visit::whereDate('created_at', today())->count();

        // Device stats
        $devices = Visit::select('device')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('device')
            ->get();

        // Referrers stats
        $referrers = Visit::select('referrer')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Pages stats
        $pages = Visit::select('page_url')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('page_url')
            ->orderByDesc('total')
            ->take(10)
            ->get();
        // Pages stats
        $todaypages = Visit::whereDate('created_at', today())
            ->select('page_url')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('page_url')
            ->orderByDesc('total')
            ->get();

        // Locations stats
        $locations = Visit::select('country')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();
        // cities
        $cities = Visit::select('city')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();

        return view('admin.analytics', compact(
            'totalVisitors',
            'totalPageViews',
            'todayVisitors',
            'todayPageViews',
            'devices',
            'referrers',
            'pages',
            'locations',
            'cities',
            'todaypages',
        ));
    }
    public function orderAdd()
    {
        $products = products::all();
        $delivery_areas = delivery_areas::all();

        return view('admin.order-add', compact('products', 'delivery_areas'));
    }
    public function orderStore(Request $request)
    {
        $order = new Order();
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->note = $request->note;
        $order->save();
        return redirect()->route('admin.orders')->with('status', 'Order Created Successfully');
    }

    public function bulkOrderStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:orders,id',
            'status' => 'required|in:' . implode(',', $this->availableOrderStatuses()),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();
        $orders = Order::whereIn('id', $validated['ids'])->get();

        foreach ($orders as $order) {
            $this->applyOrderStatus($order, $validated['status']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order Status Updated Successfully',
        ]);
    }
    public function orderBlacklistUpdate($id)
    {
        $order = Order::find($id);
        $customer = Customer::where('phone', $order->phone)->first();
        $device = Device::where('user_agent', $order->user_agent)->first();


        if (!$customer) {
            $customer = new Customer();
            $customer->name = $order->name;
            $customer->phone = $order->phone;
            $customer->save();
            $customer->blackLists()->create([
                'reason' => 'Blacklist from Order by ' . auth()->user()->name,
            ]);

        } elseif ($customer->isBlocked == 0) {
            $customer->blackLists()->create([
                'reason' => 'Blacklist from Order by ' . auth()->user()->name,
            ]);
        }

        if (!$device) {
            $device = new Device();
            $device->user_agent = $order->user_agent;
            $device->customer_id = $customer->id;
            $device->save();
            $device->blackLists()->create([
                'reason' => 'Blacklist from Order by ' . auth()->user()->name,
            ]);
        } elseif ($device->isBlocked == 0) {
            $device->blackLists()->create([
                'reason' => 'Blacklist from Order by ' . auth()->user()->name,
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Order Blacklisted Successfully',
            'customer' => $customer,
            'device' => $device,
            'DeviceisBlocked' => $device->isBlocked,
            'CustomerisBlocked' => $customer->isBlocked,
        ]);
    }
    public function unblockOrder($id)
    {
        $order = Order::find($id);
        $customer = Customer::where('phone', $order->phone)->first();
        $device = Device::where('user_agent', $order->user_agent)->first();
        if($customer && $device){

        $customer->blackLists()->delete();
        $device->blackLists()->delete();
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Customer or Device is not Blacklisted',
            ]);
        }



        return response()->json([
            'success' => true,
            'message' => 'Order Unblocked Successfully',
            'customer' => $customer,
            'device' => $device,
            'DeviceisBlocked' => $device->isBlocked,
            'CustomerisBlocked' => $customer->isBlocked,
        ]);
    }
}
