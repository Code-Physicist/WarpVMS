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
<div class="row" v-cloak>
  <div class="col-sm-3">
    <div class="card mb-4 border-0 bg-violet">
      <div class="card-body text-white">
        <div class="d-flex justify-content-center text-center">
          <div class="position-absolute top-0 start-0 p-3">
            <i class="icon-mail fs-1 lh-1"></i>
          </div>
          <div class="py-3">
            <h1>{total_invitations}</h1>
            <h6>Total invitations (Last 365 days)</h6>
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
            <i class="icon-business fs-1 lh-1"></i>
          </div>
          <div class="py-3">
            <h1>{total_departments}</h1>
            <h6>Total Departments</h6>
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
            <i class="icon-layers fs-1 lh-1"></i>
          </div>
          <div class="py-3">
            <h1>{total_tenants}</h1>
            <h6>Total tenants</h6>
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
            <i class="icon-user fs-1 lh-1"></i>
          </div>
          <div class="py-3">
            <h1>{total_operators}</h1>
            <h6>Total operators</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title">Visiting Data (Last 7 Days)</h5>
      </div>
      <div class="card-body">
        <div ref="bar_graph"></div>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
<script src="{{ asset('js/apexcharts.min.js') }}"></script>
<script>
const { createApp } = Vue;
createApp({
    data() {
      return {
        total_invitations: 0,
        total_departments: 0,
        total_tenants: 0,
        total_operators: 0,
        chart: null,
        chart_options: {
          chart: {
            height: 300,
            type: "bar",
            toolbar: {
              show: false,
            },
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            curve: "smooth",
            width: 1,
            colors: ["#0a50d8", "#57637B", "#D6DAE3"],
          },
          series: [
          ],
          grid: {
            borderColor: "#ccd2da",
            strokeDashArray: 5,
            xaxis: {
              lines: {
                show: true,
              },
            },
            yaxis: {
              lines: {
                show: false,
              },
            },
            padding: {
              top: 0,
              right: 0,
              bottom: 10,
              left: 0,
            },
          },
          xaxis: {
            categories: [
            ],
          },
          yaxis: {
            labels: {
              show: false,
            },
          },
          colors: ["#eaf1ff", "#e2e5ec", "#eff1f6"],
          markers: {
            size: 0,
            opacity: 0.3,
            colors: ["#eaf1ff", "#e2e5ec", "#eff1f6"],
            strokeColor: "#ffffff",
            strokeWidth: 2,
            hover: {
              size: 7,
            },
          },
        },
      }
    },
    mounted() {
      this.get_totals();
      this.chart = new ApexCharts(this.$refs.bar_graph, this.chart_options);
      this.chart.render();
      this.get_v_stats();
    },
    methods: {
        async get_totals() {
          try {
            const response = await axios.post("dashboard/get_totals");
            if(response.data.status === "T")
            {
              this.total_invitations = response.data.total_invitations;
              this.total_departments = response.data.total_departments;
              this.total_tenants = response.data.total_tenants;
              this.total_operators = response.data.total_operators;
            }
            else if(response.data.status === "I")
            {
              window.location.href = "{{url('/admin/logout')}}";
            }
            else
            {
              
            }
          }
          catch(error) {
            console.log(error);
            this.total_invitations = "N/A";
            this.total_departments = "N/A";
            this.total_tenants = "N/A";
            this.total_operators = "N/A";
          }
        },
        async get_v_stats() {
          try {
            const response = await axios.post("dashboard/get_v_stats");
            if(response.data.status === "T")
            {
              this.update_chart(response.data.categories, response.data.expected, response.data.arrived);
            }
            else if(response.data.status === "I")
            {
              window.location.href = "{{url('/admin/logout')}}";
            }
            else
            {
              
            }
          }
          catch(error) {
            console.log(error);
          }
        },
        update_chart(categories, expected, arrived) {
          this.chart.updateOptions(
            {
              series: [
                {
                  name: "Expected",
                  data: expected,
                },
                {
                  name: "Arrived",
                  data: arrived,
                },
              ],
              xaxis: {
                categories: categories,
              },
            }
          );
        }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop