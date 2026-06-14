@extends('layouts.app')

@section('content')
<style>
    .chat-layout { height: calc(100vh - 140px); min-height: 600px; background: #fff; display: flex; border: 1px solid #e2e8f0; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .contact-list-column { width: 320px; border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; background: #fff; flex-shrink: 0; }
    .search-box-area { padding: 20px; border-bottom: 1px solid #f8fafc; }
    .contact-scroll-area { flex: 1; overflow-y: auto; padding: 15px; }
    .contact-item { padding: 15px; border-radius: 16px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; text-decoration: none; color: inherit; display: flex; align-items: center; }
    .contact-item:hover { background-color: #f8fafc; }
    .contact-item.active { background-color: #eff6ff; border-color: #dbeafe; }
    .chat-window-column { flex: 1; display: flex; flex-direction: column; background: #fff; position: relative; }
    .chat-header { height: 80px; padding: 0 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #fff; z-index: 10; }
    .chat-history { flex: 1; overflow-y: auto; padding: 30px; background-color: #ffffff; background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px); background-size: 24px 24px; display: flex; flex-direction: column; }
    .chat-input-area { height: 90px; padding: 0 30px; background: #fff; border-top: 1px solid #f1f5f9; display: flex; align-items: center; z-index: 10; }
    .message-bubble { max-width: 70%; padding: 12px 18px; margin-bottom: 15px; position: relative; font-size: 0.95rem; line-height: 1.6; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .bubble-them { background: #fff; color: #1e293b; border-radius: 18px 18px 18px 4px; border: 1px solid #e2e8f0; align-self: flex-start; }
    .bubble-me { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-radius: 18px 18px 4px 18px; align-self: flex-end; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.15); }
    .message-time { font-size: 0.7rem; margin-top: 4px; opacity: 0.8; display: block; }
    .bubble-me .message-time { text-align: right; color: rgba(255,255,255,0.85); }
    .avatar-circle { width: 45px; height: 45px; border-radius: 50%; background: #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; }
    .chat-bar-wrapper { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px 15px; display: flex; align-items: center; }
    .chat-input { border: none; background: transparent; flex: 1; padding: 0 15px; outline: none; height: 45px; }
    .btn-send { width: 40px; height: 40px; border-radius: 50%; border: none; background: #4f46e5; color: white; display: flex; align-items: center; justify-content: center; }
    .attachment-preview img { max-width: 100%; border-radius: 12px; margin-bottom: 8px; border: 1px solid rgba(255,255,255,0.2); }
    .contact-scroll-area::-webkit-scrollbar, .chat-history::-webkit-scrollbar { width: 5px; }
    .contact-scroll-area::-webkit-scrollbar-thumb, .chat-history::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>

<div class="container-fluid pb-4 h-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.messages_title') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.stay_in_touch') }}</p>
        </div>
    </div>

    <div class="chat-layout">
        <div class="contact-list-column">
            <div class="search-box-area">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="{{ __('messages.search_teacher') }}">
                </div>
            </div>
            
            <div class="contact-scroll-area">
                <small class="text-uppercase text-muted fw-bold px-2 mb-3 d-block" style="font-size: 0.7rem;">{{ __('messages.teachers') }}</small>
                
                @forelse($teachers as $teacher)
                    <a href="{{ route('parent.communication', ['teacher_id' => $teacher->teacher_id]) }}" class="contact-item {{ (isset($activeTeacher) && $activeTeacher->teacher_id == $teacher->teacher_id) ? 'active' : '' }}">
                        <div class="avatar-circle me-3 bg-primary text-white shadow-sm">
                            {{ substr($teacher->full_name ?? $teacher->username ?? 'T', 0, 1) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $teacher->full_name ?? $teacher->username }}</h6>
                            </div>
                            <small class="text-muted text-truncate d-block">{{ __('messages.tap_to_chat') }}</small>
                        </div>
                    </a>
                @empty
                    <div class="text-center p-4 text-muted">{{ __('messages.no_teachers_found') }}</div>
                @endforelse
            </div>
        </div>

        <div class="chat-window-column">
            @if(isset($activeTeacher))
                <div class="chat-header">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3 bg-primary text-white">
                            {{ substr($activeTeacher->full_name ?? $activeTeacher->username, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">{{ $activeTeacher->full_name ?? $activeTeacher->username }}</h5>
                            <div class="d-flex align-items-center">
                                <span class="bg-success rounded-circle me-1" style="width: 8px; height: 8px;"></span>
                                <small class="text-muted">{{ __('messages.online') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chat-history" id="messagesContainer">
                    <div class="text-center mb-4 mt-2">
                        <span class="badge bg-light text-secondary border fw-normal px-3 py-1 rounded-pill">{{ __('messages.chat_history') }}</span>
                    </div>

                    @forelse($messages as $msg)
                        @php $isMe = ($msg->sender_type == 'App\Models\Guardian'); @endphp
                        <div class="message-bubble {{ $isMe ? 'bubble-me' : 'bubble-them' }}">
                            @if($msg->attachment)
                                @php $ext = pathinfo($msg->attachment, PATHINFO_EXTENSION); @endphp
                                <div class="attachment-preview">
                                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ asset('storage/'.$msg->attachment) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$msg->attachment) }}" alt="Image">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/'.$msg->attachment) }}" target="_blank" class="btn btn-sm {{ $isMe ? 'btn-light' : 'btn-primary' }} mb-2 w-100 text-start">
                                            <i class="bi bi-file-earmark-arrow-down-fill me-2"></i> {{ __('messages.download') }} {{ strtoupper($ext) }}
                                        </a>
                                    @endif
                                </div>
                            @endif

                            {{ $msg->message_content }}
                            
                            <span class="message-time">
                                {{ $msg->created_at->format('h:i A') }} 
                                @if($isMe) <i class="bi bi-check-all ms-1"></i> @endif
                            </span>
                        </div>
                    @empty
                        <div class="text-center mt-5">
                            <i class="bi bi-chat-dots fs-1 text-muted opacity-25"></i>
                            <p class="text-muted small mt-2">{{ __('messages.no_messages_yet') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="chat-input-area">
                    <form action="{{ route('parent.chat.send') }}" method="POST" enctype="multipart/form-data" class="chat-bar-wrapper">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $activeTeacher->teacher_id }}">
                        
                        <label class="btn btn-link text-secondary p-0 me-2 mb-0" style="cursor: pointer;">
                            <i class="bi bi-paperclip fs-5" id="clip-icon"></i>
                            <input type="file" name="attachment" class="d-none" id="fileInput" onchange="updateFileIcon()">
                        </label>

                       <input type="text" name="message" class="chat-input" placeholder="{{ __('messages.type_message') }}" autocomplete="off">
                        
                        <button type="submit" class="btn-send"><i class="bi bi-send-fill fs-6 ps-1"></i></button>
                    </form>
                </div>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="bi bi-chat-left-text fs-1 text-primary opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">{{ __('messages.select_a_teacher') }}</h5>
                    <p class="small">{{ __('messages.choose_teacher_sidebar') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function updateFileIcon() {
        const fileInput = document.getElementById('fileInput');
        const icon = document.getElementById('clip-icon');
        if (fileInput.files.length > 0) {
            icon.classList.remove('bi-paperclip');
            icon.classList.add('bi-file-earmark-check-fill', 'text-success');
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        var objDiv = document.getElementById("messagesContainer");
        if(objDiv) { objDiv.scrollTop = objDiv.scrollHeight; }
    });
</script>
@endsection