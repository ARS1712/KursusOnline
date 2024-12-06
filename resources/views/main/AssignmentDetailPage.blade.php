@extends('layout.master')
@section('content')
    <div class="container my-2">
        <div class="position-relative">
            <a href="{{ route('courseDetailPage.assignment', ['course_id' => $assignment->course->id]) }}" style="left: 0;">
                <img src="{{ asset('images/BackArrow.png') }}" alt="Back Arrow" style="width: 25px;">
            </a>
        </div>
        <h4>{{$assignment->course->name}}</h4>
        @if (session('success'))
            <div class="alert alert-success mt-3 mx-2">{{session('success')}}</div>
        @endif
        <div class="container d-flex flex-column my-4 gap-3" style="border: 2px solid black; border-radius: 10px; width: 100%;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h2>{{$assignment->title}}</h2>
                    @if (Auth::check() && Auth::user()->role_id == 3)
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('editAssignmentPage.view', ['assignment_id' => $assignment->id]) }}"><img src="{{ asset('images/EditIcon.png') }}" alt="" width="30px"></a>
                            <button type="submit" style="border: none; background: none; padding: 0;" data-bs-toggle="modal" data-bs-target="#deleteAssignmentModal">
                                <img src="{{ asset('images/DeleteIcon.png') }}" alt="Delete Icon" width="30px">
                            </button>
                            <div class="modal fade" id="deleteAssignmentModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this assignment?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('assignment.delete', ['assignment_id' => $assignment->id]) }}" method="POST" id="confirmDeleteForm">
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
                @if ($assignment->status == "On Going")
                    <h1 class="btn mt-2" style="background-color: rgb(61, 155, 93); color:white; font-weight: 500; border-radius: 20px;">{{$assignment->status}}</h1>
                @elseif ($assignment->status == "Expired")
                    <h1 class="btn mt-2" style="background-color: rgb(203, 45, 45); color:white; font-weight: 500; border-radius: 20px;">{{$assignment->status}}</h1>
                @elseif ($assignment->status == "Coming Soon")
                    <h1 class="btn mt-2" style="background-color: rgb(220, 170, 32); color:white; font-weight: 500; border-radius: 20px;">{{$assignment->status}}</h1>
                @endif
            </div>
            <div class="row">
                <div class="col-6">
                    <h5>Start</h5>
                    <h6 class="text-muted">{{$assignment->start_date->format('j F Y')}}</h6>
                </div>
                <div class="col-6">
                    <h5>End</h5>
                    <h6 class="text-muted">{{$assignment->due_date->format('j F Y')}}</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <h5>Attempts</h5>
                    @if (Auth::user()->role_id != 2)
                        @if ($assignment->attempts != null)
                            <h6 class="text-muted">{{$assignment->attempts}}</h6>
                        @else
                            <h6 class="text-muted">Unlimited</h6> 
                        @endif
                    @else
                        @if ($assignment->attempts != null)
                            @if ($submission == null)
                                <h6 class="text-muted">0 of {{$assignment->attempts}}</h6>
                            @else
                                <h6 class="text-muted">{{$submission->attempt_number}} of {{$assignment->attempts}}</h6>    
                            @endif
                        @else
                            @if ($submission == null)
                                <h6 class="text-muted">0 of Unlimited</h6>
                            @else
                                <h6 class="text-muted">{{$submission->attempt_number}} of Unlimited</h6>    
                            @endif
                        @endif
                    @endif
                </div>
                @if (Auth::check() && Auth::user()->role_id == 3)
                    <div class="col-6">
                        <h5>Total Submission</h5>
                        @if ($assignment->submissions->isNotEmpty())
                            <h6 class="text-muted">{{count($assignment->submissions)}}</h6>
                        @else
                            <h6 class="text-muted">0</h6> 
                        @endif
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col">
                    <h5>Question</h5>
                    <div class="d-flex flex-row justify-content-between p-3 mb-2" style="max-width: 300px; border-radius: 10px; background-color: rgb(242, 239, 239)">
                        <h6>{{$file_name}}</h6>
                        <a href="{{ route('assignment.download', ['assignment_id' => $assignment->id]) }}"><img src="{{ asset('images/DownloadIcon.png') }}" alt="" width="30px"></a>
                    </div>
                </div>
            </div>
            @if (Auth::check() && Auth::user()->role_id == 2)
                <hr>
                <div class="d-flex justify-content-end">
                    @if ($assignment->status == "On Going")
                        @if ($submission == null)
                            <a href="{{ route('submissionPage.view', ['assignment_id' => $assignment->id]) }}" class="btn btn-primary mb-3">Start Attempt (1)</a>
                        @else
                            @if ($submission->attempt_number < $assignment->attempts || $assignment->attempts === null)
                                <a href="{{ route('submissionPage.view', ['assignment_id' => $assignment->id]) }}" class="btn btn-primary mb-3">Start Attempt ({{$submission->attempt_number + 1}})</a>
                            @else
                                <a href="#" class="btn btn-primary disabled mb-3" aria-disabled="true">Start Attempt ({{$submission->attempt_number + 1}})</a>
                            @endif
                        @endif
                    @elseif($assignment->status == "Expired")
                        @if ($submission == null)
                            <a href="#" class="btn btn-primary disabled mb-3" aria-disabled="true">Start Attempt (1)</a>
                        @else
                            <a href="#" class="btn btn-primary disabled mb-3" aria-disabled="true">Start Attempt ({{$submission->attempt_number + 1}})</a>
                        @endif
                    @elseif($assignment->status == "Coming Soon")
                        <a href="#" class="btn btn-primary disabled mb-3" aria-disabled="true">Start Attempt (1)</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    @if (Auth::check() && Auth::user()->role_id == 3)
        @include('component.SubmissionList', ['submissions' => $submissions])
    @elseif (Auth::check() && Auth::user()->role_id == 2 && $submission != null)    
        @include('component.SubmissionHistory', ['submission' => $submission])
    @endif
    @include('component.WhiteSpace')
@endsection