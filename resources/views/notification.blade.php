<div class="container">
    <form action="{{ route('notification.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label>To:</label>
            <input name="noti_to" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label>Title</label>
            <input name="title" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label>Content</label>
            <input name="content" type="text" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>