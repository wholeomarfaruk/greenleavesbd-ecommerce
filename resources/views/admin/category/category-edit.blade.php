@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>category infomation</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="#">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="#">
                            <div class="text-tiny">categorys</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit Category</div>
                    </li>
                </ul>
            </div>
            <!-- new-category -->
            <div class="wg-box">
                <form class="form-new-product form-style-1 needs-validation"
                    action="{{ route('admin.categories.update', ['id' => $category->id]) }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                
                    <input type="hidden" hidden name="id" value="{{ $category->id }}" />
                    <fieldset class="name">
                        <div class="body-title">{{ __('Category Name') }} <span class="tf-color-1">*</span></div>
                        <input class="flex-grow @error('name') is-invalid @enderror" type="text"
                            placeholder="category name" name="name" tabindex="0" aria-required="true"
                            value="{{ $category->name }}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>



                    <fieldset class="name">
                        <div class="body-title">Status</div>
                        <div class="select flex-grow">
                            <select class=" @error('status') is-invalid @enderror" name="status" required>

                                <option value="1" {{ old('status', $category->is_active) == '1' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="0" {{ old('status', $category->is_active) == '0' ? 'selected' : '' }}>
                                    Inactive</option>

                            </select>

                        </div>
                        @error('status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title">is Show on Home Page</div>
                        <div class="select flex-grow">
                            <select class=" @error('is_homepage_show') is-invalid @enderror" name="is_homepage_show"
                                required>

                                <option value="1"
                                    {{ old('is_homepage_show', $category->is_homepage_show) == '1' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="0"
                                    {{ old('is_homepage_show', $category->is_homepage_show) == '0' ? 'selected' : '' }}>No
                                </option>

                            </select>

                        </div>
                        @error('is_homepage_show')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title">is Show on menu bar</div>
                        <div class="select flex-grow">
                            <select class=" @error('is_show_in_menu') is-invalid @enderror" name="is_show_in_menu" required>

                                <option value="1"
                                    {{ old('is_show_in_menu', $category->is_show_in_menu) == '1' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="0"
                                    {{ old('is_show_in_menu', $category->is_show_in_menu) == '0' ? 'selected' : '' }}>No
                                </option>

                            </select>

                        </div>
                        @error('is_show_in_menu')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                     <fieldset class="name">
                        <div class="body-title">Display Order <span class="tf-color-1">*</span></div>
                        <input class="flex-grow @error('display_order') is-invalid @enderror" type="number"
                            placeholder="Display order" name="display_order" tabindex="0" aria-required="true"
                            value="{{ $category->display_order }}" required autocomplete="display_order" autofocus>
                        @error('display_order')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                     <fieldset>
                        <div class="body-title">Upload image <span class="tf-color-1">*</span>
                        </div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" >
                                <img src="{{ asset('storage/images/category/' . $category->image) }}" class="effect8" alt="">
                            </div>
                            <div class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>

                                    <span class="body-text">Drop your images here or select <span
                                            class="tf-color">click to browse</span></span>
                                    <input class="@error('image') is-invalid @enderror" type="file" id="myFile" name="image" accept=".jpg, .jpeg, .png, .svg, .webp">
                                </label>
                            </div>
                        </div>
                        @error('image')

                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </fieldset>
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- content area end -->
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#myFile').on('change', function() {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imgpreview').show();
                    $('#imgpreview img').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

        })

        function stringtoSlug(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing spaces
            str = str.toLowerCase();
            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            console.log(str);

            $('#slug_input').val(str);

        }
    </script>
@endpush
