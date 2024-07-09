<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bootstrap Gallery - Adminify Bootstrap Dashboard Template</title>

    <link rel="shortcut icon" href="assets/images/favicon.svg" />

    <!-- Icomoon Font Icons css -->
    <link rel="stylesheet" href="{{ mix('fonts/icomoon/style.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ mix('css/main.min.css') }}" />
    <style>
      .select-search {
        position: relative;
      }
      .select-search span {
        cursor: pointer;
      }
      .select-search div {
        position: absolute;
        width: 100%;
        max-height: 150px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ccc;
      }
      .select-search ul {
        width: 100%;
        padding: 0;
        margin: 0;
        list-style-type: none;
      }
      .select-search li {
        padding: 5px;
        cursor: pointer;
      }
      .select-search li:hover {
        background: #eee;
      }
    </style>
    
  </head>

  <body>
    <!-- Page wrapper start -->
    <div class="page-wrapper">

      <!-- Main container start -->
      <div class="main-container">

        <!-- Sidebar wrapper start -->
        <nav id="sidebar" class="sidebar-wrapper">

          <!-- App brand starts -->
          <div class="app-brand px-3 py-2 d-flex align-items-center">
            <a href="index.html">
              <img src="assets/images/logo.svg" class="logo" alt="Bootstrap Gallery" />
            </a>
          </div>
          <!-- App brand ends -->

          <!-- Sidebar menu starts -->
          <div class="sidebarMenuScroll">
            <ul class="sidebar-menu">
              <li class="active current-page">
                <a href="index.html">
                  <i class="icon-roofing"></i>
                  <span class="menu-text">Dashboard</span>
                </a>
              </li>
            </ul>
          </div>
          <!-- Sidebar menu ends -->

        </nav>
        <!-- Sidebar wrapper end -->

        <!-- App container starts -->
        <div class="app-container">

          <!-- App header starts -->
          <div class="app-header d-flex align-items-center">

            <!-- Toggle buttons start -->
            <div class="d-flex">
              <button class="btn btn-outline-success toggle-sidebar" id="toggle-sidebar">
                <i class="icon-menu"></i>
              </button>
              <button class="btn btn-outline-danger pin-sidebar" id="pin-sidebar">
                <i class="icon-menu"></i>
              </button>
            </div>
            <!-- Toggle buttons end -->

            <!-- App brand sm start -->
            <div class="app-brand-sm d-md-none d-sm-block">
              <a href="index.html">
                <img src="assets/images/logo-sm.svg" class="logo" alt="Bootstrap Gallery">
              </a>
            </div>
            <!-- App brand sm end -->

            <!-- App header actions start -->
            <div class="header-actions">

              <div class="dropdown ms-3">
                <a class="dropdown-toggle d-flex align-items-center" href="#!" role="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="assets/images/user2.png" class="img-3x m-2 ms-0 rounded-5" alt="Admin Templates" />
                  <div class="d-md-flex d-none flex-column">
                    <span>Wilbert Williams</span>
                    <small>Admin</small>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-sm shadow-sm gap-3">
                  <a class="dropdown-item d-flex align-items-center py-2" href="profile.html"><i
                      class="icon-gitlab fs-4 me-3"></i>User Profile</a>
                  <a class="dropdown-item d-flex align-items-center py-2" href="account-settings.html"><i
                      class="icon-settings fs-4 me-3"></i>Account Settings</a>
                  <a class="dropdown-item d-flex align-items-center py-2" href="login.html"><i
                      class="icon-log-out fs-4 me-3"></i>Logout</a>
                </div>
              </div>
            </div>
            <!-- App header actions end -->

          </div>
          <!-- App header ends -->

          <!-- App body starts -->
          <div class="app-body">

            <!-- Container starts -->
            <div class="container-fluid">

              <!-- Row start -->
              <div class="row">
                <div class="col-12 col-xl-12">
                  <h2 class="mb-2">Analytics Dashboard</h2>
                  <h6 class="mb-4 fw-light">
                    A collection of visualizations showing your website data.
                  </h6>
                </div>
              </div>
              <!-- Row end -->
          <div id="app">
              <div class="row">
                <div class="col">
                  <div class="card mb-4">
                    <div class="card-header">
                      <h5 class="card-title">Edit Patient</h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">เลขบัตรปชช.</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">Passport</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">คำนำหน้า</label>
                            <select class="form-select">
                              <option value="0">เลือก</option>
                              <option value="1">นาย</option>
                              <option value="2">นาง</option>
                              <option value="3">นางสาว</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">ชื่อ *</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">ชื่อกลาง</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">นามสกุล *</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">อายุ</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">อาชีพ</label>
                            <select class="form-select">
                              <option value="0">เลือก</option>
                              <option value="1">อาชีพ 1</option>
                              <option value="2">อาชีพ 2</option>
                              <option value="3">อาชีพ 3</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">กรุ๊ปเลือด</label>
                            <select class="form-select">
                              <option value="0">เลือก</option>
                              <option value="1">A</option>
                              <option value="2">B</option>
                              <option value="3">AB</option>
                              <option value="3">O</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">ประเภทลูกค้า</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">สิทธิการรักษา</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">แพ้ยา</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">โรคประจำตัว</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">ที่อยู่</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">จังหวัด</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">อำเภอ</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">ตำบล</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">เบอร์โทรบ้าน</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">เบอร์มือถือ</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">อีเมล</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">Line ID</label>
                            <input type="text" class="form-control" id="abc" placeholder="Enter text">
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">Select 1</label>
                            <select-search :options="options1" @select="handleSelect1"></select-search>
                          </div>
                        </div>
                        <div class="col-sm-3 col-12">
                          <div class="mb-3">
                            <label class="form-label" for="abc">Select 2</label>
                            <select-search :options="options2" @select="handleSelect2"></select-search>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <div class="d-flex gap-2 justify-content-end my-4">
                              <button type="button" class="btn btn-outline-secondary">
                                ยกเลิก
                              </button>
                              <button type="button" class="btn btn-primary">
                                บันทึกข้อมูล
                              </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
              <!-- Row start -->

              <!-- Row end -->

            </div>
            <!-- Container ends -->

          </div>
          <!-- App body ends -->

          <!-- App footer start -->
          <div class="app-footer">
            <span>© Bootstrap Gallery 2024</span>
          </div>
          <!-- App footer end -->

        </div>
        <!-- App container ends -->

      </div>
      <!-- Main container end -->

    </div>
    <!-- Page wrapper end -->

    <!-- *************
			************ JavaScript Files *************
		************* -->
    <script src="{{ mix('js/jquery.min.js') }}"></script>
    
    <!-- Custom JS files -->
    <script src="{{ mix('js/custom.js') }}"></script>
    <script src="{{ mix('js/vue.global.prod.js') }}"></script>
    <script src="{{ mix('js/select-search.js') }}"></script>
    <script>
      const { createApp } = Vue;

      // Create Vue App
      const app = createApp({
        components: {
          SelectSearch,
        },
        data() {
            return {
              options1: ["Apple", "Banana", "Cherry", "Date", "Grape"],
              options2: ["Kiwi", "Lemon", "Mango", "Orange", "Pineapple"],
            };
        },
        methods: {
          handleSelect1(option) {
            console.log("Selected from first select search:", option);
          },
          handleSelect2(option) {
            console.log("Selected from second select search:", option);
          },
        },
});

app.mount("#app");
    </script>
  </body>

</html>