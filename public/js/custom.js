//creating a post
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createPostForm');
    const submitBtn = document.getElementById('submitPostBtn');
    const postContent = document.getElementById('postContent');
    const mediaInput = document.getElementById('mediaInput');
    const preloader = document.getElementById('preloader');

    // Disable the submit button initially
    submitBtn.disabled = true;

    // Function to toggle the submit button
    function toggleSubmitButton() {
        if (postContent.value.trim() !== '' || mediaInput.files.length > 0) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Add event listeners to enable/disable the submit button
    postContent.addEventListener('input', toggleSubmitButton);
    mediaInput.addEventListener('change', toggleSubmitButton);

    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();
        // Clear previous errors
        clearErrors();

        // Show the preloader
        preloader.style.display = 'flex';

        // Create a FormData object from the form
        const formData = new FormData(form);

        // Send a POST request using AJAX
        fetch(postStoreRoute, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw response;
                }
                return response.json();
            })
            .then(data => {
                preloader.style.display = 'none';
                if (data.message) {
                    // Close the modal after successful submission
                    $('#createPost').modal('hide');

                    // Show the success modal
                    $('#successModal').modal('show');

                    // Close the success modal after 2 seconds
                    setTimeout(function () {
                        $('#successModal').modal('hide');
                    }, 2000); 
                } else if (data.errors) {
                    // Display errors in the form
                    displayErrors(data.errors);
                } else {
                    throw new Error('Failed to create post');
                }
            })
            .catch(error => {
                preloader.style.display = 'none';
                console.error('Error:', error);
                error.json().then(errorData => {
                    if (errorData.errors) {
                        displayErrors(errorData.errors);
                    } else {
                        alert('Failed to create post');
                    }
                });
            });
    });

    function clearErrors() {
        const errorElements = document.querySelectorAll('.form-error');
        errorElements.forEach(element => element.remove());
    }

    function displayErrors(errors) {
        Object.keys(errors).forEach(key => {
            const error = errors[key][0];
            const inputField = document.querySelector(`[name="${key}"]`);
            if (inputField) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'form-error text-danger mt-2';
                errorDiv.innerText = error;
                inputField.parentElement.appendChild(errorDiv);
            }
        });
    }
});
    

document.addEventListener('DOMContentLoaded', function () {
    // Initialize countdown timers
    document.querySelectorAll('.countdown').forEach(function (element) {
        let startDate = new Date(element.getAttribute('data-start-date')).getTime();

        let countdownFunction = setInterval(function () {
            let now = new Date().getTime();
            let distance = startDate - now;

            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            element.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

            if (distance < 0) {
                clearInterval(countdownFunction);
                element.innerHTML = "Class Started";
            }
        }, 1000);
    });
});


    //delete post

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-post').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const postId = this.getAttribute('data-id');
                const deleteUrl = deletePostRoute.replace(':post_id', postId);
    
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your post has been deleted.',
                                    'success'
                                ).then(() => {
                                    
                                    // Reload the page
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'There was a problem deleting your post.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'There was a problem deleting your post.',
                                'error'
                            );
                        });
                    }
                });
            });
        });
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#classTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    });
    

    //delete notification
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-notification');
    
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const notificationId = this.getAttribute('data-id');
                const deleteUrl = notificationDeleteRoute.replace(':id', notificationId);
    
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your notification has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'There was a problem deleting your notification.',
                                    'error'
                                );
                            }
                        }).catch(error => {
                            Swal.fire(
                                'Error!',
                                'There was a problem deleting your notification.',
                                'error'
                            );
                        });
                    }
                });
            });
        });
    });
    

 // searching tbooke content
 document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('title');
    const creatorInput = document.getElementById('creator');
    const categoriesInput = document.getElementById('categories');

    const filterContent = () => {
        const title = titleInput.value.trim().toLowerCase();
        const creator = creatorInput.value.trim().toLowerCase();
        const categories = categoriesInput.value.trim().toLowerCase();


        document.querySelectorAll('.content-item').forEach(item => {
            const itemTitle = item.getAttribute('data-title').toLowerCase();
            const itemCreatorFirstName = item.getAttribute('data-creator-first-name').toLowerCase();
            const itemCreatorLastName = item.getAttribute('data-creator-last-name').toLowerCase();
            const itemFullCreatorName = `${itemCreatorFirstName}${itemCreatorLastName}`.toLowerCase();
            const itemCategories = item.getAttribute('data-categories').toLowerCase();

            const titleMatch = title === '' || itemTitle.includes(title);

            // Match creator by first name, last name, or full name including space
            const fullName = `${itemCreatorFirstName}${itemCreatorLastName}`.toLowerCase();
            const reversedFullName = `${itemCreatorLastName}${itemCreatorFirstName}`.toLowerCase();
            const trimmedSearchValue = creator.replace(/\s+/g, '').toLowerCase();

            const creatorMatch = trimmedSearchValue === '' ||
                fullName.includes(trimmedSearchValue) ||
                reversedFullName.includes(trimmedSearchValue) ||
                itemFullCreatorName.includes(trimmedSearchValue);

            const categoriesMatch = categories === '' || itemCategories.includes(categories);


            if (titleMatch && creatorMatch && categoriesMatch) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    };

    // Event listeners for input fields
    titleInput.addEventListener('input', filterContent);
    creatorInput.addEventListener('input', filterContent);
    categoriesInput.addEventListener('input', filterContent);
});

// searching tbooke content
document.addEventListener('DOMContentLoaded', function () {
    const itemNameInput = document.getElementById('itemName');
    const sellerNameInput = document.getElementById('sellerName');
    const itemPriceInput = document.getElementById('itemPrice');

    const filterResources = () => {
        const itemName = itemNameInput.value.trim().toLowerCase();
        const sellerName = sellerNameInput.value.trim().toLowerCase();
        const itemPrice = itemPriceInput.value.trim().toLowerCase();

        document.querySelectorAll('.col-12.col-md-3').forEach(resource => {
            const resourceItemName = resource.getAttribute('data-item-name').toLowerCase();
            const resourceSellerFullName = resource.getAttribute('data-seller-full-name').toLowerCase();
            const resourceItemPrice = resource.getAttribute('data-item-price').toLowerCase();

            // Split seller full name into first name and last name
            const [sellerFirstName, sellerLastName] = resourceSellerFullName.split(' ');

            // Match item name, seller name (first name, last name, or full name), and item price
            const itemNameMatch = itemName === '' || resourceItemName.includes(itemName);
            const fullName = `${sellerFirstName}${sellerLastName}`.toLowerCase();
            const reversedFullName = `${sellerLastName}${sellerFirstName}`.toLowerCase();
            const trimmedSellerName = sellerName.replace(/\s+/g, '').toLowerCase();
            const sellerNameMatch = trimmedSellerName === '' ||
                fullName.includes(trimmedSellerName) ||
                reversedFullName.includes(trimmedSellerName) ||
                resourceSellerFullName.includes(trimmedSellerName);
            const itemPriceMatch = itemPrice === '' || resourceItemPrice.includes(itemPrice);

            if (itemNameMatch && sellerNameMatch && itemPriceMatch) {
                resource.style.display = '';
            } else {
                resource.style.display = 'none';
            }
        });
    };

    // Event listeners for input fields
    itemNameInput.addEventListener('input', filterResources);
    sellerNameInput.addEventListener('input', filterResources);
    itemPriceInput.addEventListener('input', filterResources);


});




//Applying to become a creator
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('creatorModeForm');
    const submitBtn = document.getElementById('submitRequestBtn');

    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();
        // Create a FormData object from the form
        const formData = new FormData(form);

        // Send a POST request using fetch
        fetch(creatorApplicationRoute, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Close the modal after successful submission
                    $('#creatorMode').modal('hide');

                    // Show the success modal
                    $('#successModalApplication').modal('show');

                    setTimeout(function () {
                        $('#successModalApplication').modal('hide');

                        // Redirect to the tbooke-learning page
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    throw new Error('Application failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Application failed');
            });
    });
});



// reloading page after creationg post
$(document).ready(function () {
    $('#successModal').on('hidden.bs.modal', function () {
        // Remove the modal backdrop
        $('.modal-backdrop').remove();

        // Reset the form after modal is closed
        var form = document.getElementById('createPostForm');
        form.reset();

         // Refresh the page after dismissing the success modal
         location.reload();
    });
})

$(document).ready(function () {
    $('#successModalApplication').on('hidden.bs.modal', function () {
        // Remove the modal backdrop
        $('.modal-backdrop').remove();

        // Reset the form after modal is closed
        var form = document.getElementById('creatorModeForm');
        form.reset();

         // Refresh the page after dismissing the success modal
         location.reload();
    });
})


//posting a comment/reply

$(document).ready(function () {
    $('.comment-toggle-btn').click(function (e) {
        e.preventDefault();
        $(this).closest('.d-flex').find('.comment-box').toggle();
    });

});


// Posting comments

document.addEventListener('DOMContentLoaded', function () {
    // Select all comment forms and corresponding submit buttons
    const commentForms = document.querySelectorAll('[id^="createCommentForm"]');
    const submitButtons = document.querySelectorAll('[id^="submitCommentBtn"]');

    // Loop through each form and button pair
    commentForms.forEach((form, index) => {
        const submitCommentBtn = submitButtons[index]; // Get the corresponding submit button

        submitCommentBtn.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(form);

            fetch(commentStoreRoute, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => {
                if (response.ok) {
                    console.log('Comment submitted successfully');
                    window.location.reload();
                } else {
                    throw new Error('Failed to create comment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to create comment');
            });
        });
    });
});

//textarea
    tinymce.init({
        selector: '.tinymce-textarea', 
        plugins: 'advlist autolink lists link image charmap print preview anchor',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        menubar: false,
    });


    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('followForm');
        const submitBtn = document.getElementById('followButton');
    
        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            // Create a FormData object from the form
            const formData = new FormData(form);
    
            // Send a Follow request using AJAX
            fetch(userFollowRoute, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Following failed');
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    alert('Following failed');
                });
        });
    });    

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('unfollowForm');
        const submitBtn = document.getElementById('unfollowButton');
    
        submitBtn.addEventListener('click', function (e) {

            e.preventDefault(); 

            // Create a FormData object from the form
            const formData = new FormData(form);
    
            // Send Unfollow request using AJAX
            fetch(userunfollowRoute, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('unfollowing failed');
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    alert('unfollowing failed');
                });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const generalalertNotifications = document.getElementById('generalalertNotifications');
    
        if (generalalertNotifications) {
            generalalertNotifications.addEventListener('click', function () {
                // Update DOM immediately
                document.querySelector('.indicator').innerText = '';
    
                fetch(notificationsClear, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                    // Optionally update the UI further if needed
                })
                .catch(error => console.error('Error:', error));
            });
        }
    });
    
      document.addEventListener('DOMContentLoaded', function () {
        const messageNotifications = document.getElementById('messagesNotifications');
    
        if (messageNotifications) {
            messageNotifications.addEventListener('click', function () {
                // Update DOM immediately
                document.querySelector('.message-indicator').innerText = '';
    
                fetch(messagenotificationsClear, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                    // Optionally update the UI further if needed
                })
                .catch(error => console.error('Error:', error));
            });
        }
    });

 //like and unlike
 $(document).ready(function() {
    $('.like-unlike-form').submit(function(event) {
        event.preventDefault(); 

        var form = $(this);
        var action = form.attr('action');
        var method = form.attr('method');
        var data = form.serialize();
        var postId = form.data('post-id');
        var actionLike = form.data('action-like');
        var actionUnlike = form.data('action-unlike');

        // Original post like count
        var likeCountElement = $('#likes-count-' + postId);

        // Reposted post like count
        var likeCountElementRepost = $('#likes-count-repost-' + postId);

        // Parse current like counts for both original and repost
        var currentCountOriginal = parseInt(likeCountElement.text().trim());
        var currentCountRepost = parseInt(likeCountElementRepost.text().trim());

        // Like/unlike buttons for repost
        var repostLikeButton = $('#likeButton-repost-' + postId);
        var repostUnlikeButton = $('#unlikeButton-repost-' + postId);

        $.ajax({
            url: action,
            method: method,
            data: data,
            success: function(response) {
                if (action === actionLike) {
                    // Change to unlike button for the original post
                    form.attr('action', actionUnlike);
                    form.find('button').attr('id', 'unlikeButton-' + postId)
                        .removeClass('like-btn').addClass('unlike-btn engage-unlike-btn')
                        .html('<span class="d-none d-md-inline"><i class="feather-sm" data-feather="thumbs-down"></i> Unlike</span><span class="d-inline d-md-none"><i class="feather-sm" data-feather="thumbs-down"></i></span>');
                    
                    // Change to unlike button for the reposted post
                    repostLikeButton.attr('id', 'unlikeButton-repost-' + postId)
                        .removeClass('like-btn').addClass('unlike-btn engage-unlike-btn')
                        .html('<span class="d-none d-md-inline"><i class="feather-sm" data-feather="thumbs-down"></i> Unlike</span><span class="d-inline d-md-none"><i class="feather-sm" data-feather="thumbs-down"></i></span>');
                    
                    // Increment the like count for both original and repost
                    currentCountOriginal += 1;
                    currentCountRepost += 1;
                } else {
                    // Change to like button for the original post
                    form.attr('action', actionLike);
                    form.find('button').attr('id', 'likeButton-' + postId)
                        .removeClass('unlike-btn engage-unlike-btn').addClass('like-btn')
                        .html('<span class="d-none d-md-inline"><i class="feather-sm" data-feather="thumbs-up"></i> Like</span><span class="d-inline d-md-none"><i class="feather-sm" data-feather="thumbs-up"></i></span>');
                    
                    // Change to like button for the reposted post
                    repostUnlikeButton.attr('id', 'likeButton-repost-' + postId)
                        .removeClass('unlike-btn engage-unlike-btn').addClass('like-btn')
                        .html('<span class="d-none d-md-inline"><i class="feather-sm" data-feather="thumbs-up"></i> Like</span><span class="d-inline d-md-none"><i class="feather-sm" data-feather="thumbs-up"></i></span>');
                    
                    // Decrement the like count for both original and repost
                    currentCountOriginal -= 1;
                    currentCountRepost -= 1;
                }

                // Update the like count in the DOM for both original and repost
                likeCountElement.html('<i class="feather-sm" data-feather="thumbs-up"></i> ' + currentCountOriginal);
                likeCountElementRepost.html('<i class="feather-sm" data-feather="thumbs-up"></i> ' + currentCountRepost);

                // Reinitialize feather icons
                feather.replace();
            },
            error: function(xhr, status, error) {
                console.log('Error:', error); 
            }
        });
    });
});


    $(document).ready(function () {
        $('.repost-form').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            var postId = form.data('post-id');
    
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
    
                    // Show the success modal on repost
                    $('#successModalonRepost').modal('show');
    
                    // Close the success modal after 2.2 seconds
                    setTimeout(function () {
                        $('#successModalonRepost').modal('hide');
                        // Reload the page after the modal is closed
                        location.reload();
                    }, 2200);
    
                    // Update the repost count for the specific post
                    var postContainer = $('#post-' + postId);
                    var repostCountElement = postContainer.find('#repost-count-' + postId);
    
                    if (repostCountElement.length === 0) {
                        // If the element doesn't exist, create it
                        var commentStats = postContainer.find('.comment-stats.float-end');
                        commentStats.append('<a class="text-muted repost-count" id="repost-count-' + postId + '" href="#">1 Repost</a>');
                    } else {
                        // If the element exists, update its count
                        var currentCount = parseInt(repostCountElement.text().trim().split(' ')[0]) || 0;
                        repostCountElement.text((currentCount + 1) + ' Reposts');
                    }
    
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
    
    

    document.addEventListener('DOMContentLoaded', function() {
        var selectprofileType = document.querySelector('.profile-type select');
        var instituionDetails = document.getElementById('institution-details');
    
        selectprofileType.addEventListener('change', function() {
            if (selectprofileType.value === 'institution') {
                instituionDetails.style.display = 'block';
            } else {
                instituionDetails.style.display = 'none';
            }
        });
    });




    document.addEventListener('DOMContentLoaded', function () {
        const mediaInput = document.getElementById('media_files');
        const previewContainer = document.getElementById('media-preview');
    
        mediaInput.addEventListener('change', function(event) {
            previewContainer.innerHTML = ''; // Clear existing previews
    
            const files = event.target.files;
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
    
                    reader.onload = function(e) {
                        const filePreview = document.createElement('div');
                        filePreview.classList.add('file-preview');
    
                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('img-thumbnail');
                            img.style.width = '100px';
                            img.style.height = '100px';
                            img.style.margin = '10px';
                            filePreview.appendChild(img);
                        } else {
                            const icon = document.createElement('span');
                            if (file.type === 'application/pdf') {
                                icon.innerHTML = '<i class="fa fa-file-pdf-o" style="font-size:48px;color:red"></i>';
                            } else if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type === 'application/msword') {
                                icon.innerHTML = '<i class="fa fa-file-word-o" style="font-size:48px;color:blue"></i>';
                            } else if (file.type === 'application/vnd.ms-powerpoint' || file.type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
                                icon.innerHTML = '<i class="fa fa-file-powerpoint-o" style="font-size:48px;color:orange"></i>';
                            } else if (file.type.startsWith('video/')) {
                                icon.innerHTML = '<i class="fa fa-file-video-o" style="font-size:48px;color:gray"></i>';
                            } else {
                                icon.innerHTML = '<i class="fa fa-file-o" style="font-size:48px;color:black"></i>';
                            }
                            filePreview.appendChild(icon);
                        }
    
                        previewContainer.appendChild(filePreview);
                    };
    
                    reader.readAsDataURL(file);
                }
            }
        });
    });
    
        document.getElementById('mediaInput').addEventListener('change', function(event) {
        const container = document.getElementById('selectedImagesContainer');
        container.innerHTML = ''; // Clear the container

        const files = event.target.files;
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-thumbnail');
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.margin = '10px';
                    img.style.objectFit = 'contain';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        }
    });
    
    lightbox.option({
      'resizeDuration': 200,
      'wrapAround': true
    })

    

    

    
   
    