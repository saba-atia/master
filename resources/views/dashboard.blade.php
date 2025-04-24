{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}



<!--
=========================================================
* Argon Dashboard 3 - v2.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
@extends('dash.dash')

@section('contentdash')

{{-- الشرط: إذا المستخدم هو Admin --}}
<div class="container-fluid py-4">
  <!-- محتوى Admin -->
  @if(auth()->user()->role == 'admin')
  <div class="row">
      <!-- إحصائيات -->
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card bg-gradient-primary border-0">
              <div class="card-body p-3">
                  <div class="row">
                      <div class="col-8">
                          <div class="numbers">
                              <p class="text-sm mb-0 text-white text-uppercase font-weight-bold">Total Employees</p>
                              <h5 class="text-white font-weight-bolder">245</h5>
                              <p class="mb-0 text-white"><span class="text-white text-sm font-weight-bolder">+12%</span> since last month</p>
                          </div>
                      </div>
                      <div class="col-4 text-end">
                          <div class="icon icon-shape bg-white shadow text-center rounded-circle">
                              <i class="ni ni-single-02 text-primary text-lg opacity-10"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <!-- المزيد من الإحصائيات حسب الحاجة -->
  </div>
  <div class="row mt-4">
      <!-- Recent Requests Table -->
      <div class="col-lg-7 mb-lg-0 mb-4">
          <div class="card">
              <div class="card-header pb-0 p-3">
                  <h6 class="mb-0">Recent Requests</h6>
              </div>
              <div class="card-body p-3">
                  <div class="table-responsive">
                      <table class="table align-items-center">
                          <thead>
                              <tr>
                                  <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Employee</th>
                                  <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Request Type</th>
                                  <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Submission Date</th>
                                  <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td>
                                      <div class="d-flex px-2 py-1">
                                          <div class="d-flex flex-column justify-content-center">
                                              <h6 class="mb-0 text-sm">John Smith</h6>
                                          </div>
                                      </div>
                                  </td>
                                  <td>
                                      <p class="text-sm font-weight-normal mb-0">Annual Leave</p>
                                  </td>
                                  <td>
                                      <span class="text-sm font-weight-normal">15/06/2023</span>
                                  </td>
                                  <td>
                                      <span class="badge badge-sm bg-gradient-success">Approved</span>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
      <!-- المزيد من الأقسام حسب الحاجة -->
  </div>
  @endif

  <!-- محتوى Super Admin -->
  @if(auth()->user()->role == 'superadmin')
  <div class="container-fluid py-4">
      <h4>Welcome Super Admin 🎓</h4>
      <p>This section is under construction.</p>
      <!-- إضافة محتوى السوبر أدمن هنا حسب الحاجة -->
  </div>
  @endif

  <!-- محتوى Employee -->
  @if(auth()->user()->role == 'employee')
  <div class="container-fluid py-4">
      <h4 class="text-white font-weight-bolder">Welcome Employee 👨‍💼</h4>
      <p  class="text-white font-weight-bolder">This section is under construction.</p>
      <!-- إضافة محتوى الموظف هنا حسب الحاجة -->
  </div>
  @endif
</div>


@endsection
