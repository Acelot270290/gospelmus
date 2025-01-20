@extends('layouts.master')

@section('title', 'Home')

@section('content')
  <!-- Hero Section -->
  <section class="hero">
    <div>
      <h1>Teste</h1>
      <p>Explore songs, chords, and artists from all genres</p>
    </div>
  </section>

  <!-- Featured Section -->
  <section class="container my-5">
    <h2 class="text-center mb-4">Featured Songs webhook</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <img src="https://via.placeholder.com/300" class="card-img-top" alt="Song Image">
          <div class="card-body">
            <h5 class="card-title">Song Title 1</h5>
            <p class="card-text">Artist Name</p>
            <a href="#" class="btn btn-primary">View</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <img src="https://via.placeholder.com/300" class="card-img-top" alt="Song Image">
          <div class="card-body">
            <h5 class="card-title">Song Title 2</h5>
            <p class="card-text">Artist Name</p>
            <a href="#" class="btn btn-primary">View</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <img src="https://via.placeholder.com/300" class="card-img-top" alt="Song Image">
          <div class="card-body">
            <h5 class="card-title">Song Title 3</h5>
            <p class="card-text">Artist Name</p>
            <a href="#" class="btn btn-primary">View</a>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
