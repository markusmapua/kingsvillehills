$(document).ready(function() {

    // View Modal
    $(document).ready(function() {
        $('#viewAnnouncementModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 

        var title = button.attr('data-title');
        var date = button.attr('data-date');
        var updatedAt = button.attr('data-updated-at');
        var message = button.attr('data-message');

        var modal = $(this);
        modal.find('#viewTitle').text(title);
        
        var dateHtml = 'Posted on: ' + date;
        
        // If updatedAt exists, then append
        if (updatedAt) {
            dateHtml += ' <small class="text-warning ml-2 font-italic">(Edited on: ' + updatedAt + ')</small>';
        }

        modal.find('#viewDate').html(dateHtml);
        
        modal.find('#viewMessage').text(message);
        });
    });

    // Edit Modal
    $(document).ready(function() {
        $('#editAnnouncementModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 

            var id = button.attr('data-id');
            var title = button.attr('data-title');
            var message = button.attr('data-message');

            var modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_title').val(title);
            modal.find('#edit_message').val(message);
        });
    });

    // Delete modal
    $(document).ready(function() {
        $('#deleteAnnouncementModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var announcementId = button.attr('data-id'); 

            $('#confirmDeleteBtn').attr('href', 'includes/delete_announcement.php?id=' + announcementId);
        });
    });
});
