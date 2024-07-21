@extends('layout')
@section('title')
Dashboard
@stop
@section('header')
Dashboard
@stop
@section('sub_header')
Information summary
@stop
@section('content')
<div id="app">
<div class="row">
                <div class="col-sm-3">
                  <div class="card mb-4 border-0 bg-violet">
                    <div class="card-body text-white">
                      <div class="d-flex justify-content-center text-center">
                        <div class="position-absolute top-0 start-0 p-3">
                          <i class="icon-pie-chart fs-1 lh-1"></i>
                        </div>
                        <div class="py-3">
                          <h1>100</h1>
                          <h6>Weekly new visitors</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="card mb-4 border-0 bg-primary">
                    <div class="card-body text-white">
                      <div class="d-flex justify-content-center text-center">
                        <div class="position-absolute top-0 start-0 p-3">
                          <i class="icon-shopping-bag fs-1 lh-1"></i>
                        </div>
                        <div class="py-3">
                          <h1>100</h1>
                          <h6>Weekly new departments</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="card mb-4 border-0 bg-purple">
                    <div class="card-body text-white">
                      <div class="d-flex justify-content-center text-center">
                        <div class="position-absolute top-0 start-0 p-3">
                          <i class="icon-shopping-cart fs-1 lh-1"></i>
                        </div>
                        <div class="py-3">
                          <h1>100</h1>
                          <h6>Weekly new tenants</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="card mb-4 border-0 bg-warning">
                    <div class="card-body text-white">
                      <div class="d-flex justify-content-center text-center">
                        <div class="position-absolute top-0 start-0 p-3">
                          <i class="icon-twitch fs-1 lh-1"></i>
                        </div>
                        <div class="py-3">
                          <h1>100</h1>
                          <h6>Weekly new operators</h6>
                        </div>
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
        
      }
    },
    mounted() {
    },
    methods: {
        
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop