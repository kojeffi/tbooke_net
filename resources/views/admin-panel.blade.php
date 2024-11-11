@include('includes.admin-header')
{{-- Sidebar --}}
@include('includes.admin-sidebar')

{{-- Topbar --}}
<div class="main">
    @include('includes.admin-topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Dashboard</h1>

            {{-- Stats Row --}}
            <div class="row mb-4">
                {{-- Total Registered Users, New Users This Month, Active & Inactive Users --}}
                <div class="col-md-3">
                    <div class="card bg-primary text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="users" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Total Registered Users</h5>
                                <h6>{{ $totalUsers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="calendar" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>New Users This Month</h5>
                                <h6>{{ $newUsersThisMonth }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="check-circle" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Active Users</h5>
                                <h6>{{ $totalActiveUsers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="slash" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Inactive Users</h5>
                                <h6>{{ $totalInactiveUsers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Breakdown by User Type --}}
            <div class="row mb-4">
                {{-- Total Students, Teachers, Institutions, Others --}}
                <div class="col-md-3">
                    <div class="card bg-warning text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="user" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Total Students</h5>
                                <h6>{{ $totalStudents }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="book" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Total Teachers</h5>
                                <h6>{{ $totalTeachers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="home" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Total Institutions</h5>
                                <h6>{{ $totalInstitutions }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white animate-hover">
                        <div class="card-body d-flex align-items-center">
                            <i class="align-middle text-white" data-feather="layers" style="width: 36px; height: 36px;"></i>
                            <div class="ms-3">
                                <h5>Total Others</h5>
                                <h6>{{ $totalOthers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="row">
                <div class="col-12 col-lg-8 col-xxl-9">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Users</h5>
                        </div>
                        <div class="card-body">
                            <div class="align-self-center chart chart-lg">
                                <canvas id="userTypeChart" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Graph Column --}}
                <div class="col-12 col-lg-4 col-xxl-3">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Numbers of Users Per Period</h5>
                        </div>
                        <div class="card-body d-flex w-100">
                            <div class="align-self-center chart chart-lg">
                                <canvas id="userChart" width="369" height="525" style="display: block; height: 350px; width: 246px;" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- footer --}}
    @include('includes.footer')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userChart').getContext('2d');
        const userCountPerMonth = @json(array_values($userCountPerMonth));
        const userChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'New Users',
                    data: userCountPerMonth,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.yLabel;
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .animate-hover {
        transition: transform 0.2s ease-in-out;
    }

    .animate-hover:hover {
        transform: scale(1.05);
    }

    .text-white h5, .text-white h6 {
        color: #fff !important;
    }
    #userTypeChart {
    max-width: 350px;
    max-height: 350px;
    width: 100%;
    height: auto;
    margin: 0 auto;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userTypeChart').getContext('2d');
        
        const userTypeData = {
            students: @json($totalStudents),
            teachers: @json($totalTeachers),
            institutions: @json($totalInstitutions),
            others: @json($totalOthers)
        };

        const userTypeChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Students', 'Teachers', 'Institutions', 'Others'],
                datasets: [{
                    data: [userTypeData.students, userTypeData.teachers, userTypeData.institutions, userTypeData.others],
                    backgroundColor: [
                        '#fcb92c',  // Yellow for Students
                        '#6c757d',  // Blue for Teachers
                        '#212529',  // Green for Institutions
                        '#17a2b8'   // Red for Others
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    });
</script>
