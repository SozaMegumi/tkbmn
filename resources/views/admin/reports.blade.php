@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>System Reports</h3>
        
        <div class="d-flex gap-2">
            <select class="form-select" id="monthFilter" onchange="updateDashboard()">
                <option value="all">All Months</option>
                <option value="jan">January</option>
                <option value="feb">February</option>
                <option value="mar" selected>March</option>
            </select>
            <select class="form-select" id="classFilter" onchange="updateDashboard()">
                <option value="all">All Classes</option>
                <option value="mawar">Kelas Mawar</option>
                <option value="melur">Kelas Melur</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <span>Attendance Trends</span>
                    <span class="badge bg-light text-info fs-6" id="avgAttendance">92% Avg</span>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">Hafazan Performance</div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <canvas id="academicChart" height="200"></canvas>
                    <div class="mt-4 w-100">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Passed Hafazan
                                <span class="badge bg-success rounded-pill" id="passedCount">15</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Needs Improvement
                                <span class="badge bg-danger rounded-pill" id="failedCount">3</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>Detailed Student Report</span>
                    <button class="btn btn-sm btn-outline-light">Export to PDF</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Attendance (%)</th>
                                    <th>Hafazan Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>STU-001</td>
                                    <td>Ahmad Bin Abdullah</td>
                                    <td>Kelas Mawar</td>
                                    <td><span class="text-success fw-bold">95%</span></td>
                                    <td><span class="badge bg-success">Passed</span></td>
                                    <td><button class="btn btn-sm btn-primary">View Profile</button></td>
                                </tr>
                                <tr>
                                    <td>STU-002</td>
                                    <td>Nurul Aminah</td>
                                    <td>Kelas Melur</td>
                                    <td><span class="text-warning fw-bold">78%</span></td>
                                    <td><span class="badge bg-danger">Needs Improvement</span></td>
                                    <td><button class="btn btn-sm btn-primary">View Profile</button></td>
                                </tr>
                                <tr>
                                    <td>STU-003</td>
                                    <td>Muhammad Hafiz</td>
                                    <td>Kelas Mawar</td>
                                    <td><span class="text-success fw-bold">100%</span></td>
                                    <td><span class="badge bg-success">Passed</span></td>
                                    <td><button class="btn btn-sm btn-primary">View Profile</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Wait for the DOM to load before drawing charts
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Initialize Attendance Bar Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], // X-axis labels
                datasets: [{
                    label: 'Attendance Rate (%)',
                    data: [95, 88, 92, 98], // Dummy data - replace with Laravel variable later
                    backgroundColor: 'rgba(13, 202, 240, 0.5)', // Bootstrap Info color
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // 2. Initialize Academic Doughnut Chart
        const academicCtx = document.getElementById('academicChart').getContext('2d');
        const academicChart = new Chart(academicCtx, {
            type: 'doughnut',
            data: {
                labels: ['Passed', 'Needs Improvement'],
                datasets: [{
                    data: [15, 3], // Your original numbers
                    backgroundColor: [
                        '#198754', // Bootstrap Success
                        '#dc3545'  // Bootstrap Danger
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false // Hide default legend since we built a custom HTML one below it
                    }
                }
            }
        });
    });

    // 3. Logic Function: Simulating what happens when a user clicks a filter
    function updateDashboard() {
        const month = document.getElementById('monthFilter').value;
        const className = document.getElementById('classFilter').value;
        
        // In a real application, you would use AJAX/Fetch here to ask your Laravel Controller 
        // for new data based on the selected month and class, then update the charts.
        console.log(`Filtering data for Month: ${month}, Class: ${className}`);
        
        // Just a visual example to show the user the filter "did" something
        alert("In the final version, this filter will trigger an AJAX request to your Laravel backend to update the charts below!");
    }
</script>
@endsection