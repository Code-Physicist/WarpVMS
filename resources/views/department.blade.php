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
                        Short Name
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        Full Name
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        Parent Department
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Floors
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Tel 1
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Tel 2
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-settings me-2 fs-4"></span>
                        Action
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(dept, index) in depts">
                    <td>{index+1}.</td>
                    <td>{dept.short_name}</td>
                    <td>{dept.full_name}</td>
                    <td>{dept.sup_dept_name}</td>
                    <td>{display_val(dept.floor)}</td>
                    <td>{display_val(dept.phone1)}</td>
                    <td>{display_val(dept.phone2)}</td>
                    <td>
                      <a @click="show_edit(dept)" class="btn btn-primary"><span class="icon-edit"></span></a>
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
          <div class="d-flex justify-content-center">
            <div class="w-50">
              @if($admin_level_id != '2' and $admin_level_id != '3')
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Parent Department *</label>
                  <input type="text" class="form-control" v-model.trim="dept.sup_dept_id"/>
                </div>
              </div>
              @endif
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Full Name *</label>
                  <input type="text" class="form-control" v-model.trim="dept.full_name"/>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Short Name *</label>
                  <input type="text" class="form-control" v-model.trim="dept.dept_name"/>
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
                    <label class="form-label">Phone No. 1 *</label>
                    <input type="text" class="form-control" v-model.trim="dept.phone1"/>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label class="form-label">Phone No. 2</label>
                    <input type="text" class="form-control" v-model.trim="dept.phone2"/>
                  </div>
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
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/utils.js') }}"></script>
<script>
const { createApp } = Vue;
createApp({
    data() {
      return {
        active_ui: 1,
        sup_depts: [],
        filter: {
          status: 2
        },
        depts:[],
        dept:{
            dept_name: "",
            full_name: "",
            sup_dept_id: 0,
            floor: "",
            phone1: "",
            phone2: ""
        },
        dept0: null,
      }
    },
    mounted() {
@if($admin_level_id != '2' and $admin_level_id != '3')
      this.get_sup_depts();
@endif
      this.dept0 = { ...this.dept };
      this.get_depts();
    },
    methods: {
      show_create() {
        this.active_ui = 2;
        MyApp.copy_vals(this.dept0, this.dept);
      },
      show_edit(dept) {
        this.active_ui = 2;
        console.log(dept);
      },
      async get_sup_depts() {
        try {
          const response = await axios.post("/admin/get_sup_depts");
          this.sup_depts = response.data.data_list;
        }
        catch(error) {
          console.log(error);
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