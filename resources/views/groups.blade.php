@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')
@include('sweetalert::alert')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
   <main class="content">
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-3">Groups</h1>
        </div>
        <div class="mb-3">
            <a href="{{ route('group.create') }}" class="btn btn-primary">Create Group</a>
              <!-- Button to show my groups -->
            <a href="{{ route('groups.myGroups') }}" class="btn btn-secondary">My Groups</a>
        </div>

        {{-- Available Groups --}}
        <div class="row">
            @forelse ($groups as $group)
                <div class="col-lg-3 col-md-3 col-sm-6 mb-4">
                    <div class="card text-center groups-card">
                        <a href="{{ route('groups.show', $group->slug) }}">
                            <img src="{{ $group->thumbnail ? asset('storage/' . $group->thumbnail) : asset('default-images/group.png') }}" alt="{{ $group->name }} thumbnail" class="card-img-top img-fluid groups-thumbnail" style="height: 150px; object-fit: cover;">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">{{ $group->name }}</h5>
                            <p class="card-text">{{ Str::limit($group->description, 18) }}</p>
                            <a href="{{ route('groups.show', $group->slug) }}" class="btn btn-sm btn-secondary">View</a>

                            {{-- Check if the user has already joined the group --}}
                            @if($group->members->contains($user->id))
                                {{-- If the user is a member, show "Joined" button and disable it --}}
                                <button class="btn btn-sm btn-success" disabled>Joined</button>
                            @else
                                {{-- If the user is not a member, show the "Join" button --}}
                                <form action="{{ route('groups.join', $group->slug) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Join</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p>No groups available.</p>
            @endforelse
        </div>
    </div>
</main>

    {{-- footer --}}
    @include('includes.footer')
</div>
