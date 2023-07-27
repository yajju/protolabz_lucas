<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Link of Css -->
  <link rel="stylesheet" href="{{asset('public/css/main.css')}}">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <title>{{ __('message.Forgot Password') }} </title>
</head>

<body class="bg">
  <div class="main-section">
    <!-- nav-section start  -->
    <!-- <div class="nav-section">
      <div class="container-fluid  darkyellow border border-dark">
        <div class="row  py-2 px-2">
          <div class="col-12">
            <h5><img src="./IMG/logo.png" alt=""></h5>
          </div>
        </div>
      </div>
    </div> -->
    <!-- nav-section end  -->
 
    <div class="article-section ">
      <div class="container first">
      <form method="post" action="{{url('User/forgotpassword')}}">
      @csrf

        <div class="row  d-flex justify-content-center">
     
       
          <div class="col-md-6 col-lg-5 col-xl-4 rounded shadowbx1 py-4">
            <h5 class="reg-page"><img  src="{{asset('public/img/logo.png')}}" alt=""></h5>
            <h2 class="text-center font-weight-bold purplebx">{{ __('message.Forgot Password') }} </h2>
            <div class="form-group">
              <label for="exampleInputEmail1" class="mb-0  dark_text">{{ __('message.email') }} </label>
              <!-- <input type="text" class="form-control border border-dark rounded-0 inside " id="exampleInputEmail1 " -->
                <!-- aria-describedby="emailHelp"> -->
                <input type="text" class="form-control border border-dark rounded-0 inside " name="email" 
               aria-describedby="emailHelp">
            </div>

            <div class="btn-box  ">
              <input type="submit" name="submit">
              <!-- <a href="{{url('/reset-password')}}" class="text-white"> <button type="button"
                  class=" primary w-100  px-3 py-2 font-weight-bold  rounded border-0 text-white">Submit</button></a> -->
            </div>
            <div class="container-fluid ">
                <div class="row justify-content-center mt-3">
                  or
                </div>
                <div class="row justify-content-center">
                  <a href="{{url('User/')}}">{{ __('message.sign in here') }} </a>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>

    </div>
  </div>
  </form>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>
</body>

</html>