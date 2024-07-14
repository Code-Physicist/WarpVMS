@extends('layout')
@section('title')
Dashboard
@stop
@section('header')
Tenants
@stop
@section('sub_header')
Create and edit tenants
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5.css') }}" />
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5-custom.css') }}" />
@stop
@section('content')
<div id="app">
  <div v-show="active_ui === 1" class="row">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-start">
              <div class="card-title">Total {tenants.length} Tenants</div>
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
                        Email
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        name
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        department
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
                  <tr v-for="(tenant, index) in tenants">
                    <td>{index+1}.</td>
                    <td>{tenant.email}</td>
                    <td>{tenant.name}</td>
                    <td>{lv1_dept_dict[tenant.dept_id]}</td>
                    <td>
                      <a @click="show_edit(tenant)" class="btn btn-primary"><span class="icon-edit"></span></a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div> 
        </div>
      </div>
    </div>
  </div>
  <div v-show="active_ui === 2" class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">Create Tenant</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-center">
            <div class="w-50">
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Department *</label>
                  <select v-model="tenant.dept_id" class="form-select">
                      <option value="0">Please select</option>
                      <option v-for="d in lv1_depts" :value="d.dept_id">
                        {d.full_name}
                      </option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Email *</label>
                  <input type="text" class="form-control" v-model.trim="tenant.email"/>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Name *</label>
                  <input type="text" class="form-control" v-model.trim="tenant.name"/>
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
        lv1_depts: [],
        lv1_dept_dict:{},
        filter: {
          status: 2
        },
        tenants:[],
        tenant:{
            id: 0,
            email: "",
            name: "",
            dept_id: 0,
        },
        tenant0: null,
      }
    },
    mounted() {
      this.tenant0 = { ...this.tenant };
      this.init_ui();
    },
    methods: {
      async init_ui() {
        try {
          await this.get_lv1_depts();
          await this.get_tenants();
        }
        catch(error) {
          console.log(error);
        }
        
      },
      show_create() {
        this.active_ui = 2;
        MyApp.copy_vals(this.tenant0, this.tenant);
      },
      show_edit(tenant) {
        this.active_ui = 2;
        console.log(tenant);
      },
      async get_lv1_depts() {
        const response = await axios.post("/admin/get_lv1_depts");
        this.lv1_depts = response.data.data_list;
      },
      async get_tenants() {
        $('#data-table').DataTable().destroy();
        this.tenants = [];
        const response = await axios.post("/admin/get_tenants", this.filter);
        this.tenants = response.data.data_list;
        for(let i = 0; i < this.tenants.length; i++)
        {
          this.lv1_dept_dict[this.tenants[i].dept_id] = this.tenants[i].full_name;
        }
        Vue.nextTick(() => {
          $('#data-table').DataTable();
        });
      },
      change_filter() {
        this.get_tenants();
      }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop