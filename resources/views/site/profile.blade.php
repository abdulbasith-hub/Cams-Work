@extends('index2')
@section('content')
@section('title', ' Dashboard')
<title>Profile Page</title>
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
<style>
    body {
        background: rgb(247, 244, 244);
        min-height: 100vh;
        font-family: 'Arial', sans-serif;
    }

    .container {
        animation: fadeIn 1s ease-in-out;
    }

    .card {
        background: linear-gradient(135deg, #ffffff, #ffffff);
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-title {
        background: linear-gradient(90deg, #ff7eb3, #ff758c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ff758c, #ff7eb3);
        border: none;
        box-shadow: 0 5px 15px rgba(255, 117, 140, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 117, 140, 0.5);
    }

    .btn-outline-danger {
        border-color: #ff758c;
        color: #ff758c;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .btn-outline-danger:hover {
        background: #ff758c;
        color: white;
        transform: translateY(-3px);
    }

    .circle {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .circle:hover {
        transform: scale(1.05);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<?php
$sessionchargedel = session('charge');
$sessionuserdel = session('user');
$session_chargid = $sessionchargedel->chargeid;
// print_r($sessionchargedel);
//print_r($sessionuserdel);

// if ($session_chargid == '1') {

// }
// print_r($sessionchargedel->chargeid);
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Profile Picture Section -->
            <div class="text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img src="../assets/images/profile/user-1.jpg" alt="Profile Picture" class="rounded-circle circle"
                        width="150" height="150" style="object-fit: cover;">

                </div>
                <h2 class="fw-bold text-dark">{{ $sessionuserdel->username }}</h2>
                <p class="text-muted fs-5">{{ $sessionchargedel->chargedescription }}</p>
            </div>

            <!-- Profile Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">Profile Information</h3>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-envelope-fill text-primary fs-4 me-3"></i>
                                <div>
                                    <div class="text-muted small">Email</div>
                                    <div class="fw-semibold">{{ $sessionuserdel->email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone-fill text-primary fs-4 me-3"></i>
                                <div>
                                    <div class="text-muted small">Phone</div>
                                    <div class="fw-semibold">{{ $sessionuserdel->mobilenumber }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-mailbox text-primary fs-4 me-3"></i>
                                <div>
                                    <div class="text-muted small">Charge Description</div>
                                    <div class="fw-semibold">{{ $sessionchargedel->chargedescription }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill text-primary fs-4 me-3"></i>
                                <div>
                                    <div class="text-muted small">District</div>
                                    <div class="fw-semibold">{{ $sessionchargedel->distename ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-bezier text-primary fs-4 me-3"></i>
                                <div>
                                    <div class="text-muted small">Region</div>
                                    <div class="fw-semibold">{{ $sessionchargedel->regionename ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-bricks text-primary fs-4 me-3"></i>
                                <div>
                                    <div class="text-muted small">Department</div>
                                    <div class="fw-semibold">{{ $sessionchargedel->deptesname ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Details Card -->
            {{-- <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Registration Details</h4>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <div class="text-muted small">Member Since</div>
                                        <div class="fw-semibold">March 15, 2023</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-heart-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <div class="text-muted small">Interests</div>
                                        <div class="fw-semibold">Technology, AI, Web Development</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

            <div class="d-flex justify-content-center gap-3">
                {{-- <button class="btn btn-primary px-4 py-2">
                        <i class="bi bi-pencil-fill me-2"></i>Edit Profile
                    </button> --}}
                {{-- <a href="/dashboard"> <button class="btn btn-outline-danger px-4 py-2">
                        <i class="bi bi-box-arrow-right me-2"></i>Back to dashboard
                    </button></a> --}}

            </div>
        </div>
    </div>
</div>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
@endsection
