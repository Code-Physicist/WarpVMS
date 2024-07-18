@extends('layout')
@section('title')
Dashboard
@stop
@section('header')
Operators
@stop
@section('sub_header')
Create and edit operators
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
              <div class="card-title">Total {operators.length} Operators</div>
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
                  <tr v-for="(operator, index) in operators">
                    <td>{index+1}.</td>
                    <td>{operator.email}</td>
                    <td>{operator.name}</td>
                    <td>{operator.full_name}</td>
                    <td>
                      <a @click="show_edit(operator)" class="btn btn-primary btn-sm"><span class="icon-edit"></span></a>
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
          <h5 class="card-title">Create Operator</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-center">
            <div class="w-50">
@if($admin_level_id != '2')
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Department *</label>
                  <select v-model="operator.dept_id" class="form-select">
                      <option value="0">Please select</option>
                      <option v-for="d in depts" :value="d.dept_id">
                        {d.full_name}
                      </option>
                  </select>
                </div>
              </div>
@endif
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Admin Type *</label>
                  <select v-model="operator.admin_level_id" class="form-select">
                      <option value="0">Please select</option>
                      <option v-for="l in admin_levels" :value="l.id">
                        {l.name}
                      </option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Email *</label>
                  <input type="text" class="form-control" v-model.trim="operator.email"/>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Name *</label>
                  <input type="text" class="form-control" v-model.trim="operator.name"/>
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
        admin_levels: [],
        depts: [],
        dept_dict:{},
        filter: {
          status: 2
        },
        operators:[],
        operator:{
          name: "",
          email: "",
          admin_level_id: 0,
          dept_id: 0,
        },
        operator0: null,
      }
    },
    mounted() {
@if($admin_level_id == '2')
      this.admin_levels = [
        {"id":"2", "name":"Building Owner"},
        {"id":"3", "name":"Building Operator"}
      ];
@elseif($admin_level_id == '4')
      this.admin_levels = [
        {"admin_level_id":"4", "name":"Tenant"},
        {"admin_level_id":"5", "name":"Tenant Operator"}
      ];
@endif
      this.operator0 = { ...this.operator };
      this.init_ui();
    },
    methods: {
      async init_ui() {
        try {
          await this.get_depts();
          await this.get_operators();
        }
        catch(error) {
          console.log(error);
        }
      },
      show_create() {
        this.active_ui = 2;
        MyApp.copy_vals(this.operator0, this.operator);
      },
      show_edit(operator) {
        this.active_ui = 2;
        console.log(operator);
      },
      async get_depts() {
        const response = await axios.post("/admin/get_operator_depts");
        this.depts = response.data.data_list;
      },
      async get_operators() {
        $('#data-table').DataTable().destroy();
        this.operators = [];
        const response = await axios.post("/admin/get_operators", this.filter);
        this.operators = response.data.data_list;
        Vue.nextTick(() => {
          $('#data-table').DataTable();
        });
      },
      change_filter() {
        this.get_operators();
      }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop