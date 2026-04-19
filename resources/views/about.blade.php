@extends('layouts.app')

@section('content')
  <!--banner start-->
<section class="about_banner" style="  background-image: linear-gradient(rgba(2, 44, 111, 0.6), rgba(2, 44, 111, 0.6)),
                    url('{{ asset('frontend/img/banner/about_banner.jpeg') }}');">
  <div class="container">
    <div class="row justify-content-center">
     <div class="col-lg-6">
      <div class="banner_h">
       <h1>About</h1>
      </div>
     </div>
    </div>
  </div>
</section>
  <!--banner end-->
  <!--about company start-->
<section class="about_company cm_padding">
 <div class="container">
  <div class="row">
   <div class="col-lg-5">
    <div class="company_box">
      <div class="company_img" style="background-image: url('{{ asset('frontend/img/about/Kamal-Hossain-CEO-of-Green-Leaves-BD.jpeg') }}');">
      </div>
      <div class="company_icon">
        <span><img class="w-100" src="{{ asset('frontend/img/about/recruit.gif') }}" alt="Green Leaves"></span><p>১০০+ পণ্য</p>
      </div>
      <div class="company_icon">
        <span><img class="w-100" src="{{ asset('frontend/img/about/earth.gif') }}" alt="Green Leaves"></span><p>৫০+ ডিলার</p>
      </div>
      <div class="company_icon">
        <span><img class="w-100" src="{{ asset('frontend/img/about/human.gif') }}" alt="Green Leaves"></span><p>২০০০০+ গ্রাহক</p>
      </div>
      <div class="company_icon">
        <span><img class="w-100" src="{{ asset('frontend/img/about/worker.gif') }}" alt="Green Leaves"></span><p>৫+ বছরের অভিজ্ঞতা</p>
      </div>
      <div class="company_icon">
        <span><img class="w-100" src="{{ asset('frontend/img/about/human.gif') }}" alt="Green Leaves"></span><p>২০০+ কর্মচারী</p>
      </div>
    </div>
   </div>
   <div class="col-lg-6  offset-lg-1">
    <div class="company_text">
      <h5>ABOUT US</h5>
     <h1>GREEN LEAVES BANGLADESH</h1>
     <p> <strong>গ্রীন লীভস বাংলাদেশ</strong> দেশের অন্যতম শীর্ষস্থানীয় এবং দ্রুত বর্ধনশীল একটি বৈচিত্র্যময় ম্যানুফ্যাকচারিং, মোড়কজাতকারী ও বাজারজাতকারী কোম্পানি। ২০২১ সালের আত্মপ্রকাশের পর থেকে এটি আধুনিক ব্যবসায়িক মডেল ও প্রযুক্তির সমন্বয়ে গড়ে উঠেছে। সময়ের সাথে সাথে এটি বাংলাদেশের বাজারে একটি নির্ভরযোগ্য নাম হিসেবে প্রতিষ্ঠিত হয়েছে।</p>
    <p>
    <strong>গ্রীন লীভস বাংলাদেশ</strong> কোম্পানির কর্পোরেট অফিস অবস্থিত বাংলাদেশের খুলনা বিভাগে। প্রতিষ্ঠানটি সারা দেশে ৫০+ টি নিজস্ব ডিলার নেটওয়ার্কের মাধ্যমে গ্রাহকদের কাছে পৌঁছে থাকে। পাশাপাশি গ্লোবাল দিক থেকেও এটি কাজ করে থাকে, যার মাধ্যমে আন্তর্জাতিক বাজারেও প্রবেশের সুযোগ তৈরি হয়েছে।</p>
    <p>
    বর্তমানে <strong>গ্রীন লীভস বাংলাদেশ</strong> কোম্পানিতে রয়েছে 2০০+ জনের বেশি দক্ষ ও অভিজ্ঞ কর্মী যারা দেশের বিভিন্ন অঞ্চলে আমাদের পণ্য বিতরণে সক্রিয়ভাবে কাজ করছে। এই সংস্থার প্রধান লক্ষ্য হচ্ছে গ্রাহকদের সর্বোচ্চ সন্তুষ্টি নিশ্চিত করা এবং সেরা মানের পণ্য সরবরাহ করা। আমাদের এই অগ্রযাত্রায় সকল গ্রাহক, পরিবেশক ও শুভানুধ্যায়ীদের প্রতি গ্রীন লীভস বাংলাদেশের পক্ষ থেকে আন্তরিক কৃতজ্ঞতা ও ধন্যবাদ।</p>
    </div>
   </div>
  </div>
 </div>
</section>
  <!--about company end-->
<!--Our Management start-->
<section class="our_management cm_padding">
 <div class="container">
  <div class="row">
   <div class="col-lg-12">
    <div class="management_head">
     <h1>Our Management</h1>
    </div>
   </div>
   <div class="col-lg-3 col-md-6">
    <div class="management_member">
     <img class="w-100" src="{{ asset('frontend/img/about/kamal_hossain.jpeg') }}" alt="Green Leaves">
     <h2>MD. KAMAL HOSSEN</h2>
     <p>MANAGING DIRECTOR</p>
     <p>kamalhos95@gmail.com</p>
    </div>
   </div>
   <div class="col-lg-3 col-md-6">
    <div class="management_member">
     <img class="w-100" src="{{ asset('frontend/img/about/monira_islam.png') }}" alt="Green Leaves">
     <h2>MONIRA ISLAM BINTY</h2>
     <p>DIRECTOR</p>
     <p>bintyislam@gmail.com</p>
    </div>
   </div>
   <div class="col-lg-3 col-md-6">
    <div class="management_member">
     <img class="w-100" src="{{ asset('frontend/img/about/shariful_islam.jpeg') }}" alt="Green Leaves">
     <h2>MD SHARIFUL ISLAM</h2>
     <p>AREA MANAGER</p>
     <p>sharifu6350@gmail.com</p>
    </div>
   </div>
   <div class="col-lg-3 col-md-6">
    <div class="management_member">
     <img class="w-100" src="{{ asset('frontend/img/about/mehedi_hasan.jpeg') }}" alt="Green Leaves">
     <h2>MEHEDI HASAN</h2>
     <p>AREA MANAGER</p>
     <p>mehedihasan@gmail.com</p>
    </div>
   </div>
  </div>
 </div>
</section>
<!--Our Management end-->
<!--about us start-->
<section class="about_us cm_padding">
 <div class="container">
  <div class="row">
  <div class="col-lg-12">
   <div class="about_us_head">
    <h1>Who we are</h1>
   </div>
  </div>
   <div class="col-lg-6">
    <div class="about_us_box">
     <div class="box_img">
      <img class="w-100" src="{{ asset('frontend/img/about/mision.jpeg') }}" alt="Green Leaves">
     </div>
     <div class="box_text">
      <h1>Mission</h1>
      <p>আমাদের লক্ষ্য হলো বাংলাদেশের বিপুল জনশক্তিকে দক্ষ মানবসম্পদে রূপান্তর করে টেকসই কর্মসংস্থানের সুযোগ সৃষ্টি করা। আমরা সর্বোচ্চ মানের পণ্য সরবরাহ, গ্রাহক সন্তুষ্টি নিশ্চিত করা এবং আধুনিক প্রযুক্তি ও উদ্ভাবনের মাধ্যমে দেশীয় ও আন্তর্জাতিক বাজারে শক্ত অবস্থান গড়ে তুলতে প্রতিশ্রুতিবদ্ধ। এর মাধ্যমে দেশের অর্থনৈতিক উন্নয়নে ইতিবাচক অবদান রাখা এবং সমাজের সার্বিক উন্নয়ন নিশ্চিত করা আমাদের অঙ্গীকার।</p>
     </div>
    </div>
   </div>
   <div class="col-lg-6">
    <div class="about_us_box">
     <div class="box_img">
      <img class="w-100" src="{{ asset('frontend/img/about/vision.jpeg') }}" alt="Green Leaves">
     </div>
     <div class="box_text">
      <h1>Vision</h1>
      <p>আমাদের ভিশন হলো বাংলাদেশে একটি শীর্ষস্থানীয় ও বিশ্বমানের ম্যানুফ্যাকচারিং, সোর্সিং ও বাজারজাতকারী প্রতিষ্ঠান হিসেবে প্রতিষ্ঠিত হওয়া। আমরা উদ্ভাবন, গুণগত মান ও আধুনিক প্রযুক্তির মাধ্যমে আন্তর্জাতিক পর্যায়ে একটি বিশ্বস্ত ব্র্যান্ড হিসেবে পরিচিত হতে চাই। গ্রাহক সন্তুষ্টি, সেবা মান উন্নয়ন এবং টেকসই প্রবৃদ্ধির মাধ্যমে দীর্ঘমেয়াদে দেশের অর্থনীতি ও সমাজে ইতিবাচক প্রভাব তৈরি করাই আমাদের লক্ষ্য।</p>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>
<!--about us end-->

<!--Newsletter start-->
<section class="news_letter cm_padding">
  <div class="container">
   <div class="row">
    <div class="col-lg-6">
      <div class="news_letter_text">
       <h5>Subscribe Newsletter</h5>
       <h1>Stay Updated with the Latest News!</h1>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="news_letter_form">
        <div class="input-group">
          <input type="email" class="form-control" placeholder="Enter your email">
          <button class="btn">Click Here</button>
        </div>
      </div>
    </div>
   </div>
  </div>
</section>
<!--Newsletter end-->
@endsection
