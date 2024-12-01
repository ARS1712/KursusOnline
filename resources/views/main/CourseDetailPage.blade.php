@extends('layout.master')
@section('content')
    <div class="container my-2">
        {{-- Judul --}}
        <div class="position-relative">
            <a href="{{ route('coursesPage.view') }}" style="left: 0;">
                <img src="{{ asset('BackArrow.png') }}" alt="Back Arrow" style="width: 25px;">
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <h4>{{$course->name}}</h4>
            @if (Auth::check() && Auth::user()->role_id == 1)
                <div class="d-flex align-items-center">
                    <a href="{{ route('editCoursePage.view', ['course_id' => $course->id]) }}"><img src="{{ asset('EditIcon.png') }}" alt="" width="30px"></a>
                    <button type="submit" style="border: none; background: none; padding: 0;" data-bs-toggle="modal" data-bs-target="#deleteCourseModal">
                        <img src="{{ asset('DeleteIcon.png') }}" alt="Delete Icon" width="30px">
                    </button>
                    <div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this course?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('courseDetailPage.delete', ['course_id' => $course->id]) }}" method="POST" id="confirmDeleteForm">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="delete_action" id="deleteAction">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            @endif
        </div>       
        @if (session('success-update'))
            <div class="alert alert-success mt-3 mx-2">{{session('success-update')}}</div>
        @endif
        {{-- Lecturer --}}
        <div class="fs-5 d-inline-flex">
            @if ($course->lecturer->photo)
                <img src="{{ asset($course->lecturer->photo) }}" alt="Lecturer's photo" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;" class="me-3">
            @else
                <img src="{{ asset('EmptyProfile.png') }}" alt="Default profile picture" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;" class="me-3">
            @endif    
            <div class="d-flex flex-column">
                <span class="fw-semibold">{{$course->lecturer->name}}</span>
                <small class="text-muted"><a href="mailto:{{$course->lecturer->email}}">{{$course->lecturer->email}}</a></small>
                <small class="text-muted">Lecturer</small>
            </div>
        </div>
        {{-- Course Tabs --}}
        @include('component.CourseTabs', ['course' => $course])
        {{-- Topic Tab --}}
        @if (request()->routeIs('courseDetailPage.view'))
            @if (Auth::check() && Auth::user()->role_id == 1)
                <div class="d-flex justify-content-start my-3">
                    <a href="{{ route('addTopicPage.view', ['course_id' => $course->id]) }}" class="btn btn-primary">Add New Topic</a>
                </div>
            @endif
            @if ($topic != null)
                @include('component.TopicsTabs', ['topics' => $course->topics, 'course' => $course])
                @include('component.TopicTabDetail', ['topic' => $topic])
            @else
                <h6>TBA</h6>
            @endif
        @endif
        {{-- Assignment Tab --}}
        @if (request()->routeIs('courseDetailPage.assignment'))
            @if (Auth::check() && Auth::user()->role_id == 3)
                <div class="d-flex justify-content-start my-3">
                    <a href="{{ route('addAssignmentPage.view', ['course_id' => $course->id]) }}" class="btn btn-primary">Add New Assignment</a>
                </div>
            @endif
            @if ($assignments->isNotEmpty())
                @include('component.AssignmentTabDetail', ['course' => $course, 'assignments' => $assignments])
            @else
                <h6>TBA</h6>
            @endif
        @endif
        {{-- Student Tab --}}
        @if (request()->routeIs('courseDetailPage.student'))
            @include('component.StudentTabDetail', ['course' => $course, 'activeStudents' => $activeStudents, 'finishedStudents' => $finishedStudents])
        @endif
        @include('component.WhiteSpace')
    </div>
@endsection