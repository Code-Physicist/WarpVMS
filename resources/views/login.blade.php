<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>BTS Visionary Park</title>

    <!-- *************
			************ CSS Files *************
		************* -->
    <!-- Icomoon Font Icons css -->
    <link rel="stylesheet" href="{{ mix('fonts/icomoon/style.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ mix('css/main.min.css') }}" />
    <style>
    .flex-grow-55 {
      flex: 0 0 55%;
    }
    .flex-grow-45 {
      flex: 0 0 45%;
    }
  </style>
  </head>

  <body style="background-color:#EEEBE8;">
    <div class="d-flex vh-100">
      <div class="flex-grow-55 vh-100">
      </div>
      <div class="flex-grow-45 vh-100 p-4" style="background-color:#1a2752">
          <form class="bg-white" action="index.html">
            <div class="rounded-3 p-4">
              <div class="login-form">
                <h4 class="my-4">Login</h4>
                <div class="mb-3">
                  <label class="form-label" for="name">Your Email <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="name" autocomplete="autocomplete"
                    placeholder="Enter your email" />
                </div>
                <div class="mb-3">
                  <label class="form-label" for="pwd">Your Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" id="pwd" placeholder="Enter password" />
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <div class="form-check m-0">
                    <input class="form-check-input" type="checkbox" value="" id="rememberPassword" />
                    <label class="form-check-label" for="rememberPassword">Remember</label>
                  </div>
                  <a href="forgot-password.html" class="text-primary text-decoration-underline">Lost password?</a>
                </div>
                <div class="d-grid py-3 mt-3">
                  <button type="submit" class="btn btn-lg btn-primary">
                    LOGIN
                  </button>
                </div>
              </div>
            </div>
          </form>
      </div>
    </div>
    <!-- Container end -->
  </body>

</html>