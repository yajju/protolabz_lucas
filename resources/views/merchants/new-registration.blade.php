@extends('merchants.layout.appframe')
@section('dashboard','active')            
            @section('main_body')
                <div class="container-fluid">


                            <div class="row justify-content-center w-100">
                                <div class="col-md-8 col-lg-6 col-xxl-3">
                                    <div class="card mt-0">
                                        <div class="card-body">
                                            @if(Session::has('errormessage'))
                                                <div class="alert alert-danger" role="alert">{{ Session::get('errormessage') }}</div>
                                            @endif

                                            @if(session('succmsg'))
                                                <div class="alert alert-success">
                                                    {!! session('succmsg') !!}
                                                </div>
                                            @endif
                                            
                                            @if(session('errmsg'))
                                                <div class="alert alert-danger">
                                                    {!! session('errmsg') !!}
                                                </div>
                                            @endif
                                            <p class="text-center"><h4 class="text-center">Change Password of your Admin Panel</h4></p>
                                            <br>
                                            <form method="get" action="{{ url('User/registration') }}"> 
                                                @csrf
                                                
                                                <div class="mb-3">
                                                    <label for="exampleInputtext1" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="exampleInputtext1" aria-describedby="textHelp">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="exampleInputEmail1" class="form-label">Email Address</label>
                                                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                                    <input type="password" class="form-control" id="exampleInputPassword1">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                <div>
            @endsection

            @section('main_script')
            <script src="{{getAssetFilePath('adminassets/libs/jquery/dist/jquery.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/sidebarmenu.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/app.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/simplebar/dist/simplebar.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/dashboard.js')}}"></script>

  <script type="text/javascript">
      window.onload = function () {
        !function (a) { a(function () { a('[data-toggle="password"]').each(function () { var b = a(this); var c = a(this).parent().find(".input-group-text"); c.css("cursor", "pointer").addClass("input-password-hide"); c.on("click", function () { if (c.hasClass("input-password-hide")) { c.removeClass("input-password-hide").addClass("input-password-show"); c.find(".fa").removeClass("fa-eye").addClass("fa-eye-slash"); b.attr("type", "text") } else { c.removeClass("input-password-show").addClass("input-password-hide"); c.find(".fa").removeClass("fa-eye-slash").addClass("fa-eye"); b.attr("type", "password") } }) }) }) }(window.jQuery);
      }
  </script>
  <script>
      // tell the embed parent frame the height of the content
      if (window.parent && window.parent.parent)
      {
          window.parent.parent.postMessage(["resultsFrame", {
            height: document.body.getBoundingClientRect().height,
            slug: "zkou4dej"
          }], "*")
      }
      // always overwrite window.name, in case users try to set it manually
      window.name = "result"
  </script>
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

            @endsection

