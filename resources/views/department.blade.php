@extends('layout')
@section('title')
Departments
@stop
@section('header')
Departments
@stop
@section('sub_header')
Create and edit departments
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5.css') }}" />
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5-custom.css') }}" />
@stop
@section('content')
<div id="app">
  <div v-show="active_ui === 1" class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-start">
              <div class="card-title">Total {depts.length} Departments</div>
              <button @click="show_create" class="btn btn-danger btn-sm mx-2"><span class="icon-add"></span> Create&nbsp;</button>
            </div>
            <div class="d-flex align-items-center justify-content-end">
              <div class="card-title text-nowrap">Status</div>
              <div>&nbsp;&nbsp;</div>
              <select @change="change_filter" v-model="filter.status" class="form-select">
                      <option value="2">All</option>
                      <option value="1">Active</option>
                      <option value="0">Disabled</option>
              </select>
            </div>
          </div>
          <hr/>
          <div class="table-responsive">
              <table class="table table-striped" id="data-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-add_task me-2 fs-4"></span>
                        Department
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Floors
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Phone No.1
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Phone No.2
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-settings me-2 fs-4"></span>
                        Status
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-settings me-2 fs-4"></span>
                        Actions
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(dept, index) in depts">
                    <td>{index+1}.</td>
                    <td>{dept.full_name}</td>
                    <td class="text-center">{display_val(dept.floor)}</td>
                    <td class="text-center">{display_val(dept.phone1)}</td>
                    <td class="text-center">{display_val(dept.phone2)}</td>
                    <td class="text-center">
                        <span v-if="dept.is_active === '1'" class="badge bg-success">Active</span>
                        <span v-else class="badge bg-secondary">Disabled</span>
                    </td>
                    <td>
                      <div class="d-flex justify-content-center align-items-center">
                        <a class="cursor-pointer" data-bs-toggle="dropdown"><h5><span class="icon-more-vertical"></span></h5></a>
                        <ul class="dropdown-menu shadow-sm dropdown-menu-mini">
                          <li><a class="dropdown-item cursor-pointer" @click="show_edit(dept)"><span class="icon-edit fs-5"></span> Edit</a></li>
                          <li><a class="dropdown-item cursor-pointer" @click="show_edit(dept)"><span class="icon-x-square fs-5"></span> Disable</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
  <div v-show="active_ui === 2" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">Create Department</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="offset-md-3 col-md-6">
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Full Name *</label>
                  <input type="text" class="form-control" v-model.trim="dept.full_name"/>
                  <div v-if="full_name_msg !== ''" class="ms-1 mt-1 text-danger">{full_name_msg}</div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Short Name *</label>
                  <input type="text" class="form-control" v-model.trim="dept.dept_name"/>
                  <div v-if="dept_name_msg !== ''" class="ms-1 mt-1 text-danger">{dept_name_msg}</div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Floor</label>
                  <input type="text" class="form-control" v-model.trim="dept.floor"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label class="form-label">Phone No.1 *</label>
                    <input type="text" class="form-control" v-model.trim="dept.phone1"/>
                    <div v-if="phone1_msg !== ''" class="ms-1 mt-1 text-danger">{phone1_msg}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label class="form-label">Phone No.2</label>
                    <input type="text" class="form-control" v-model.trim="dept.phone2"/>
                  </div>
                </div>
              </div>
              <div v-show="dept_form_message !== ''">
						    <div class="alert alert-primary d-flex align-items-center">
                    <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                    { dept_form_message }
                </div>
					    </div>
              <div class="my-3 d-flex align-items-end justify-content-center">
                <button type="button" @click="active_ui = 1" class="btn btn-outline-secondary mx-2">Cancel</button> <button type="button" @click="submit" class="btn btn-danger mx-2">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
const { createApp } = Vue;
createApp({
    data() {
      return {
        active_ui: 1,
        filter: {
          status: 2
        },
        depts:[],
        dept:{
            id: -1,
            dept_name: "",
            full_name: "",
            floor: "",
            phone1: "",
            phone2: "",
            sup_dept_id: {{$dept_id}},
        },
        dept_form_message: "",
        dept_name_msg: "",
        full_name_msg: "",
        phone1_msg: "",
        dept0: null,
      }
    },
    mounted() {
      this.dept0 = { ...this.dept };
      this.get_depts();
    },
    methods: {
      show_create() {
        this.dept_name_msg = "";
        this.full_name_msg = "";
        this.phone1_msg = "";

        MyApp.copy_vals(this.dept0, this.dept);
        this.active_ui = 2;
      },
      show_edit(dept) {
        this.dept_name_msg = "";
        this.full_name_msg = "";
        this.phone1_msg = "";

        MyApp.copy_vals(dept, this.dept);
        this.active_ui = 2;
      },
      async submit() {
        this.dept_name_msg = "";
        this.full_name_msg = "";
        this.phone1_msg = "";

        let is_valid = true;
        if (this.dept.dept_name === "") {
          this.dept_name_msg = "Short Name is blank";
          is_valid = false;
        }
        if (this.dept.full_name === "") {
          this.full_name_msg = "Full Name is blank";
          is_valid = false;
        }
        if (this.dept.phone1 === "") {
          this.phone1_msg = "Phone No.1 is blank";
          is_valid = false;
        }

        if(!is_valid) return;

        let url = (this.dept.id === -1) ? "/admin/create_department" : "/admin/update_department";
        try {
          const response = await axios.post(url, this.dept);
          if(response.data.status === "T")
          {
            this.get_depts();
            this.active_ui = 1;
          }
          else if(response.data.status === "I")
          {
            //Force log out
            window.location.href = "/admin/logout";
          }
          else
          {
            this.dept_form_message = "Invalid Data Submission";
          }
        }
        catch(error) {
          console.log(error);
          this.dept_form_message = "Server error. Please try again later";
        }

      },
      async get_depts() {
        try {
          $('#data-table').DataTable().destroy();
          this.depts = [];
          const response = await axios.post("/admin/get_departments", this.filter);
          this.depts = response.data.data_list;
          Vue.nextTick(() => {
              $('#data-table').DataTable();
          });
        }
        catch(error) {
          console.log(error);
        }
      },
      change_filter() {
        this.get_depts();
      },
      display_val(val, d_val = "-") {
        if (!val) return d_val;
        else return val;
      },
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop