<?php

use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LearningResourceController;
use App\Http\Controllers\Api\TbookeBlueboardController;
use App\Http\Controllers\Api\TbookeLearningController;
use App\Http\Controllers\Api\CreatorController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\LiveClassController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\AdminMessageController;
use App\Http\Controllers\Api\HelpCenterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupPostController;


Route::prefix('api')->namespace('Api')->group(function () {
// Authentication routes
Route::middleware('guest')->group(function () {
    // Allow GET and POST for registration
    Route::match(['get', 'post'], 'register', [RegisteredUserController::class, 'store'])->name('api.register');

    // Allow GET and POST for login
    Route::match(['get', 'post'], 'login', [AuthenticatedSessionController::class, 'store'])->name('api.login');

    // Allow GET and POST for forgot password
    Route::match(['get', 'post'], 'forgot-password', [PasswordResetLinkController::class, 'store'])->name('api.forgot-password');

    // Allow GET and POST for reset password
    Route::match(['get', 'post'], 'reset-password', [NewPasswordController::class, 'store'])->name('api.reset-password');
});

// Authenticated user routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'showOwn'])->name('api.profile.showOwn');
    Route::get('/profile/edit-profile', [ProfileController::class, 'edit'])->name('api.profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('api.profile.update');


    // Allow GET and POST for feeds
    Route::match(['get', 'post'], '/feed', [FeedController::class, 'feeds'])->name('api.feed');

    Route::post('/comment', [CommentController::class, 'store'])->name('api.comment.store');

       // Post, Like, and Repost Routes
       Route::post('/post/{id}/like', [LikeController::class, 'likePost'])->name('api.post.like');
       Route::post('/post/{id}/unlike', [LikeController::class, 'unlikePost'])->name('api.post.unlike');
       Route::post('/posts/{post}/repost', [PostController::class, 'repostPost'])->name('api.posts.repost');
       Route::post('/posts', [PostController::class, 'store'])->name('api.posts.store');
       Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('api.posts.destroy');

    Route::post('/live-classes', [LiveClassController::class, 'store'])->name('api.live-classes.store');

    Route::get('/notifications', [NotificationsController::class, 'index'])->name('api.notifications.index');
    Route::delete('/notifications/{id}', [NotificationsController::class, 'destroy'])->name('api.notifications.destroy');

    Route::post('/messages', [MessageController::class, 'store'])->name('api.messages.store');
    Route::get('/help-center', [HelpCenterController::class, 'index'])->name('api.help-center');
    Route::get('/settings', [SettingsController::class, 'index'])->name('api.settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('api.settings.update');
});


// Follow/Unfollow Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('api.profile.show');
    Route::post('/users/{user}/follow', [FollowerController::class, 'follow'])->name('api.users.follow');
    Route::post('/users/{user}/unfollow', [FollowerController::class, 'unfollow'])->name('api.users.unfollow');
    Route::post('/notifications/read', [NotificationsController::class, 'markAsRead'])->name('api.notifications.markAsRead');
    Route::post('/message-notifications/read', [NotificationsController::class, 'messagesmarkAsRead'])->name('api.notifications.messagesmarkAsRead');
});


Route::middleware(['auth:sanctum'])->group(function () {
    // API routes for learning resources
    Route::get('/learning-resources', [LearningResourceController::class, 'tbookeResources'])->name('api.learning-resources.index');
    Route::get('/learning-resources/create', [LearningResourceController::class, 'index'])->name('api.learning-resources.create');
    Route::post('/learning-resources', [LearningResourceController::class, 'store'])->name('api.learning-resources.store');
    Route::get('/learning-resources/{slug}', [LearningResourceController::class, 'show'])->name('api.learning-resources.show');
    Route::put('/learning-resources/seller/{id}', [LearningResourceController::class, 'update'])->name('api.learning-resources.update');
    Route::delete('/learning-resources/seller/delete/{id}', [LearningResourceController::class, 'deleteResource'])->name('api.learning-resources.delete');
    Route::get('/learning-resources/seller/{username}', [LearningResourceController::class, 'userResources'])->name('api.learning-resources.user');
    Route::get('/learning-resources/edit-resource/{id}', [LearningResourceController::class, 'editResource'])->name('api.learning-resources.edit');
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin-panel', [AdminController::class, 'index'])->name('api.admin.index');
    Route::post('/admin/messages/{id}/reply', [AdminMessageController::class, 'storeAdminReply'])->name('api.admin.messages.reply');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('api.subjects.store');
    Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('api.subjects.update');
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('api.subjects.destroy');
    // Route::post('/topics', [TopicController::class, 'store'])->name('api.topics.store');
    // Route::put('/topics/{id}', [TopicController::class, 'update'])->name('api.topics.update');
    // Route::delete('/topics/{id}', [TopicController::class, 'destroy'])->name('api.topics.destroy');

});


// Protected API Routes
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    // Your Tbooke Learning Routes
    // Tbooke Learning Routes
    Route::prefix('tbooke-learning')->group(function () {

        // Route to get list of all content
        Route::get('/', [TbookeLearningController::class, 'index'])->name('api.tbooke-learning.index');

        // Route to store new content
        Route::post('/', [TbookeLearningController::class, 'store'])->name('api.tbooke-learning.store');

        // Route to create content
        Route::get('/create', [TbookeLearningController::class, 'index'])->name('api.tbooke-learning.create');

        // Route to show specific content by slug
        Route::get('/{slug}', [ContentController::class, 'show'])->name('api.content.show');

        // Route to update specific content by ID
        Route::put('/creator/{id}', [TbookeLearningController::class, 'update'])->name('api.tbooke-learning.update');

        // Route to delete specific content by ID and username
        Route::delete('/creator/{username}/delete/{id}', [TbookeLearningController::class, 'deleteContent'])->name('api.tbooke-learning.delete');

        // Route to edit specific content by username and ID
        Route::get('/creator/{username}/edit/{id}', [TbookeLearningController::class, 'editContent'])->name('api.tbooke-learning.edit');

        // Route to get user-specific content by username
        Route::get('/creator/{username}', [TbookeLearningController::class, 'userContents'])->name('api.tbooke-learning.user');

    });


    Route::prefix('groups')->group(function () {
        // Group Routes
        Route::get('/', [GroupController::class, 'index'])->name('api.groups.index'); // Fetch all groups
        Route::post('/', [GroupController::class, 'store'])->name('api.groups.store'); // Create a new group
        Route::get('/create', [GroupController::class, 'creategroup'])->name('api.groups.create'); // Return create group form
        Route::get('/{slug}', [GroupController::class, 'show'])->name('api.groups.show'); // Fetch a single group by slug
        Route::post('/{group:slug}/join', [GroupController::class, 'join'])->name('api.groups.join'); // Join a group
        Route::get('/{group:slug}/edit', [GroupController::class, 'edit'])->name('api.groups.edit'); // Return edit group form
        Route::put('/{group:slug}', [GroupController::class, 'update'])->name('api.groups.update'); // Update a group
        Route::delete('/{group:slug}', [GroupController::class, 'destroy'])->name('api.groups.destroy'); // Delete a group
        Route::get('/groups/{slug}/posts', [GroupPostController::class, 'index'])->name('api.group.posts.index');


        // Group Post Routes
        Route::prefix('{slug}')->group(function () {
            Route::post('/posts', [GroupPostController::class, 'store'])->name('api.group.posts.store'); // Store a new post in the group
            Route::post('/posts/{post}/comment', [GroupController::class, 'storeComment'])->name('api.group.posts.comment'); // Comment on a post
            Route::post('/posts/{post}/like', [GroupController::class, 'likePost'])->name('api.group.posts.like'); // Like a post
            Route::post('/posts/{post}/repost', [GroupController::class, 'repostPost'])->name('api.group.posts.repost'); // Repost a post
        });
    });


    Route::get('/groups', [GroupController::class, 'index'])->name('api.groups.index');
    Route::get('/groups/create', [GroupController::class, 'creategroup'])->name('api.group.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('api.groups.store');
    Route::get('/groups/{slug}', [GroupController::class, 'show'])->name('api.groups.show');
    Route::post('/groups/{group:slug}/join', [GroupController::class, 'join'])->name('api.groups.join');

    // Group post API routes
    // Route::post('groups/{slug}/posts', [GroupPostController::class, 'store'])->name('api.group.posts.store');
    // Route::post('groups/{post}/comment', [GroupController::class, 'storeComment'])->name('api.group.posts.comment');
    // Route::post('groups/{post}/like', [GroupController::class, 'likePost'])->name('api.group.posts.like');
    // Route::post('groups/{post}/repost', [GroupController::class, 'repostPost'])->name('api.group.posts.repost');




    Route::prefix('tbooke-learning')->group(function () {
        Route::get('/live-classes', [LiveClassController::class, 'liveClasses'])->name('tbooke-live-classes.index');
    });

    // Creator Live Classes Routes
    Route::prefix('live-classes')->group(function () {
        Route::get('/', [LiveClassController::class, 'creatorClasses'])->name('live-classes.index');
        Route::get('/create', [LiveClassController::class, 'create'])->name('live-classes.create');
        Route::post('/', [LiveClassController::class, 'store'])->name('live-classes.store');
        Route::post('/{id}/register', [LiveClassController::class, 'register'])->name('live-classes.register');
        Route::get('/{slug}', [LiveClassController::class, 'showLiveClass'])->name('live-classes.show');
        Route::get('/{id}/edit', [LiveClassController::class, 'edit'])->name('live-classes.edit');
        Route::put('/{id}', [LiveClassController::class, 'update'])->name('live-classes.update');
        Route::delete('/{id}', [LiveClassController::class, 'destroy'])->name('live-classes.destroy');
    });

        // Tbooke Blueboard Routes
        Route::prefix('tbooke-blueboard')->group(function () {
            Route::get('/', [TbookeBlueboardController::class, 'index'])->name('api.tbooke-blueboard.index');
            Route::post('/store', [TbookeBlueboardController::class, 'store'])->name('api.tbooke-blueboard.store');
            Route::get('/create', [TbookeBlueboardController::class, 'create'])->name('api.tbooke-blueboard.create');
            Route::get('/{id}', [TbookeBlueboardController::class, 'show'])->name('api.tbooke-blueboard.show');
            Route::put('/{id}', [TbookeBlueboardController::class, 'update'])->name('api.tbooke-blueboard.update');
            Route::delete('/{id}', [TbookeBlueboardController::class, 'destroy'])->name('api.tbooke-blueboard.destroy');
    });

    // API Routes for SchoolsCorner
    Route::prefix('schools')->group(function () {
        Route::get('/create', [SchoolController::class, 'create'])->name('api.schools.create'); // Display creation form
        Route::get('/', [SchoolController::class, 'SchoolsCorner'])->name('api.schools.index'); // Fetch list of schools
        Route::post('/', [SchoolController::class, 'store'])->name('api.schools.store'); // Store new school
        Route::get('/{school:slug}', [SchoolController::class, 'show'])->name('api.schools.show'); // Show specific school details
    });
});
});