@extends('layout')
@section('title')
Update PDPA
@stop
@section('header')
PDPA Management
@stop
@section('sub_header')
Create/Update/Set PDPA
@stop
@section('content')
<div id="app">
  <div v-show="active_ui === 1" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-body">

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
        pdpas:[],
      }
    },
    mounted() {
      
    },
    methods: {
      show_create() {
        
      },
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop