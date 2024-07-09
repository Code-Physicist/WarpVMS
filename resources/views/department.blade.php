@extends('layout')
@section('title')
Departments
@stop
@section('header')
Departments
@stop
@section('sub_header')
Create/Edit Departments
@stop
@section('content')
<div id="app">
  <div v-show="active_ui === 1" class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-body">
          <div class="mb-2 d-flex align-items-end justify-content-between">
            <h5 class="card-title">Departments</h5>
            <button @click="show_create" class="btn btn-danger"><span class="icon-add"></span> New&nbsp;</button>
          </div>
          <div class="table-outer">
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle m-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-add_task me-2 fs-4"></span>
                        Name
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
                        Parent
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-calendar me-2 fs-4"></span>
                        Floor
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
                    <td>{dept.DeptName}</td>
                    <td>{dept.Fullname}</td>
                    <td>{dept.Tel1}</td>
                    <td>{dept.Tel2}</td>
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
  </div>
  <div v-show="active_ui === 2" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">Create Department</h5>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-sm-6">
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" v-model.trim="dept.DeptName"/>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Full Name</label>
                  <input type="text" class="form-control" v-model.trim="dept.Fullname"/>
                </div>
              </div>
            </div>
          </div>
          <div class="my-3 d-flex align-items-end justify-content-center">
            <button @click="active_ui = 1" class="btn btn-outline-secondary mx-2">Cancel</button> <button @click="submit" class="btn btn-danger mx-2">Submit</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
<script>
const { createApp } = Vue;
createApp({
    data() {
      return {
        active_ui: 1,
        depts:[],
        dept:{
            DeptName: "",
            Fullname: ""
        }
      }
    },
    mounted() {
    },
    methods: {
        show_edit(dept) {

        }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop