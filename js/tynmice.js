document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE for lessons
    tinymce.init({
        selector: 'textarea[name="lessons[]"]',
        api_key: 'yjoomyk0twspeayki2lu4xygw6h5nbjtd5s9r3ip5kpvlnmk',
        plugins: 'lists link image table',
        toolbar: 'undo redo | bold italic | bullist numlist outdent indent | link image',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Initialize TinyMCE for announcements
    tinymce.init({
        selector: 'textarea[name="announcement_messages[]"]',
        api_key: 'yjoomyk0twspeayki2lu4xygw6h5nbjtd5s9r3ip5kpvlnmk',
        plugins: 'lists link image table',
        toolbar: 'undo redo | bold italic | bullist numlist outdent indent | link image',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    

    // Add video functionality
    document.getElementById("addVideo").addEventListener("click", function() {
        let videoContainer = document.getElementById("videoContainer");
        let videoItem = document.querySelector(".video-item").cloneNode(true);
        videoItem.querySelector("input[name='video_titles[]']").value = "";
        videoItem.querySelector("input[name='video_files[]']").value = "";
        videoContainer.appendChild(videoItem);
    });

    document.getElementById("videoContainer").addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("remove-video")) {
            e.target.closest(".video-item").remove();
        }
    });

    

    // Add book functionality
    document.getElementById("addBook").addEventListener("click", function() {
        let booksContainer = document.getElementById("booksContainer");
        let bookItem = document.querySelector(".book-item").cloneNode(true);
        bookItem.querySelector("input[name='book_titles[]']").value = "";
        bookItem.querySelector("input[name='book_authors[]']").value = "";
        booksContainer.appendChild(bookItem);
    });

    document.getElementById("booksContainer").addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("remove-book")) {
            e.target.closest(".book-item").remove();
        }
    });

    // Add link functionality
    document.getElementById("addLink").addEventListener("click", function() {
        let linksContainer = document.getElementById("linksContainer");
        let linkItem = document.querySelector(".link-item").cloneNode(true);
        linkItem.querySelector("input[name='link_titles[]']").value = "";
        linkItem.querySelector("input[name='link_urls[]']").value = "";
        linksContainer.appendChild(linkItem);
    });

    document.getElementById("linksContainer").addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("remove-link")) {
            e.target.closest(".link-item").remove();
        }
    });

    // Add resource functionality
    document.getElementById("addResource").addEventListener("click", function() {
        let resourcesContainer = document.getElementById("resourcesContainer");
        let resourceItem = document.querySelector(".resource-item").cloneNode(true);
        resourceItem.querySelector("input[name='resource_titles[]']").value = "";
        resourceItem.querySelector("input[name='resource_files[]']").value = "";
        resourcesContainer.appendChild(resourceItem);
    });

    document.getElementById("resourcesContainer").addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("remove-resource")) {
            e.target.closest(".resource-item").remove();
        }
    });
    
});
