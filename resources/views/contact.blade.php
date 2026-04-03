@extends('layouts.app')

@section('content')
  <!--banner start-->
<section class="about_banner" style="background-image: linear-gradient(rgba(2, 44, 111, 0.6), rgba(2, 44, 111, 0.6)),
                    url('{{ asset('frontend/img/banner/about_banner.jpg') }}');">
  <div class="container">
    <div class="row justify-content-center">
     <div class="col-lg-6">
      <div class="banner_h">
       <h1>Contact</h1>
      </div>
     </div>
    </div>
  </div>
</section>
  <!--banner end-->
<!--contact us start-->
<section class="contact_us cm_padding">
 <div class="container">
  <div class="row">
  <div class="col-lg-12">
   <div class="about_us_head contact_us_head">
    <h1>contact information</h1>
    <h2 class="contact_us_head2">get in touch</h2>
   </div>
  </div>
   <div class="col-lg-6 order-lg-1 order-2">
    <div class="contact_us_box">
      <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d116896.60140169275!2d90.4237538913193!3d23.711022891177283!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sgreen%20leave!5e0!3m2!1sen!2sbd!4v1774979368715!5m2!1sen!2sbd"
       style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
   </div>
   <div class="col-lg-6 order-lg-2 order-1">
    <div class="contact_us2_box">
       <form>
          <!-- Full Name -->
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" placeholder="Enter your full name">
          <!-- Email -->
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="Enter your email">
          <!-- Telephone -->
            <label class="form-label">Telephone</label>
            <input type="tel" class="form-control" placeholder="Enter your phone number">
          <!-- Subject -->
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" placeholder="Enter subject">
          <!-- Message -->
            <label class="form-label">Message</label>
            <textarea class="form-control" rows="4" placeholder="Enter your message"></textarea>
          <!-- Button -->
          <div class="text-center">
            <button type="submit" class="btn cm_btn contact_button">Send Message</button>
          </div>

        </form>
    </div>
   </div>
  </div>
 </div>
</section>
<!--contact us end-->
  <!--Contact Details Start-->
  <section class="contact_details cm_padding">
   <div class="container">
    <div class="row">
     <div class="col-lg-4 col-md-4 col-12">
      <div class="service_item contact_details_item">

       <!--icon-->
       <div class="service_icon contact_details_icon">
        <i class="fa-solid fa-location-dot" style="color: rgb(0, 0, 0);"></i>
       </div>

       <!--text-->
       <div class="service_text contact_details_text">
          <h2>Office Address</h2>
          <p>315, Sher-E-Bangla Road, Sonadanga, Khulna</p>
       </div>

      </div>
     </div>
     <div class="col-lg-4 col-md-4 col-12">
      <div class="service_item contact_details_item">
       <!--icon-->
       <div class="service_icon contact_details_icon">
        <i class="fa-solid fa-phone" style="color: rgb(0, 0, 0);"></i>
       </div>
       <!--text-->
       <div class="service_text contact_details_text">
          <h2>Phone Number</h2>
          <p>+88 1893-620392 </p>
       </div>

      </div>
     </div>
     <div class="col-lg-4 col-md-4 col-12">
      <div class="service_item contact_details_item">
       <!--icon-->
       <div class="service_icon contact_details_icon">
        <i class="fa-regular fa-envelope" style="color: rgb(0, 0, 0);"></i>
       </div>
       <!--text-->
       <div class="service_text contact_details_text">
          <h2>Email Address</h2>
          <p>info@greenleavesbd.com</p>
       </div>

      </div>
     </div>
    </div>
   </div>
  </section>
  <!--Contact Details end-->
<!--Newsletter start-->
<section class="news_letter cm_padding" >
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
