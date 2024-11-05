<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LearningResourceController;
use App\Http\Controllers\TbookeBlueboardController;
use App\Http\Controllers\TbookeLearningController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LiveClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupPostController;
use App\Models\LearningResource;
use App\Models\School;

Route::get('/', function () {
    if (auth()->check()) {return redirect('/feed');  }
    return view('auth.login');
});

Route::get('/about', function () {return view('about'); });
Route::get('/privacy-policy', function () {return view('privacy-policy'); });


// Route::get('/storage-link', function(){
//     $targetFolder = storage_path('app/public');
//     $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
//     symlink($targetFolder,$linkFolder);
// });


Route::middleware(['auth', 'verified'])->group(function () {

    // Authenticated routes
    
    Route::get('/profile/edit-profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'showOwn'])->name('profile.showOwn');
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/feed', [FeedController::class, 'feeds'])->name('feed');
    Route::post('/comment', [CommentController::class, 'store'])->name('comment.store');
    
    Route::get('/live-classes', [LiveClassController::class, 'creatorClasses'])->name('live-classes.index');
    Route::get('/live-classes/create', [LiveClassController::class, 'create'])->name('live-classes.create');
    Route::post('/live-classes', [LiveClassController::class, 'store'])->name('live-classes.store');
    Route::post('/live-classes/{id}/register', [LiveClassController::class, 'register'])->name('live-classes.register');
    Route::get('/tbooke-learning/live-classes/{slug}', [LiveClassController::class, 'showLiveClass'])->name('live-classes.show');
    Route::get('/live-classes/{id}/edit', [LiveClassController::class, 'edit'])->name('live-classes.edit');
    Route::put('/live-classes/{id}', [LiveClassController::class, 'update'])->name('live-classes.update');
    Route::delete('/live-classes/{id}', [LiveClassController::class, 'destroy'])->name('live-classes.destroy');



    Route::delete('/tbooke-learning/creator/{username}/delete/{id}', [TbookeLearningController::class, 'deleteContent'])->name('tbooke-learning.delete');
    Route::get('/tbooke-learning/create', [TbookeLearningController::class, 'index'])->name('tbooke-learning.create');
    Route::get('/tbooke-learning/creator/{username}/edit/{id}', [TbookeLearningController::class, 'editContent'])->name('tbooke-learning.edit');
    Route::put('/tbooke-learning/creator/{id}', [TbookeLearningController::class, 'update'])->name('tbooke-learning.update');
    Route::get('/tbooke-learning/creator/{username}', [TbookeLearningController::class, 'userContents'])->name('tbooke-learning.user');
    Route::get('/tbooke-learning/{slug}', [ContentController::class, 'show'])->name('content.show');
    Route::get('/tbooke-learning', [TbookeLearningController::class, 'tbookeLearning'])->name('tbooke-learning');
    Route::post('/tbooke-learning', [TbookeLearningController::class, 'store'])->name('tbooke-learning.store');

    Route::delete('/learning-resources/seller/delete/{id}', [LearningResourceController::class, 'deleteResource'])->name('learning-resources.delete');
    Route::get('/learning-resources/create', [LearningResourceController::class, 'index'])->name('learning-resources.create');
    Route::get('/learning-resources/edit-resource/{id}', [LearningResourceController::class, 'editResource'])->name('learning-resources.edit');
    Route::put('/learning-resources/seller/{id}', [LearningResourceController::class, 'update'])->name('learning-resources.update');
    Route::get('/learning-resources/seller/{username}', [LearningResourceController::class, 'userResources'])->name('learning-resources.user');
    Route::get('learning-resources/{slug}', [LearningResourceController::class, 'show'])->name('learning-resources.show');
    Route::get('/learning-resources', [LearningResourceController::class, 'tbookeResources'])->name('learning-resources');
    Route::post('/learning-resources', [LearningResourceController::class, 'store'])->name('learning-resources.store');
    

    Route::post('/users/{user}/follow', [FollowerController::class, 'follow'])->name('users.follow');
    Route::post('/users/{user}/unfollow', [FollowerController::class, 'unfollow'])->name('users.unfollow');
    Route::post('/notifications/read', [NotificationsController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/message-notifications/read', [NotificationsController::class, 'messagesmarkAsRead'])->name('notifications.messagesmarkAsRead');
    
    Route::post('/post/{id}/like', [LikeController::class, 'likePost'])->name('post.like');
    Route::post('/post/{id}/unlike', [LikeController::class, 'unlikePost'])->name('post.unlike');
    Route::post('/posts/{post}/repost', [PostController::class, 'repostPost'])->name('posts.repost');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

 
    Route::get('/schools-corner/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::get('/schools-corner', [SchoolController::class, 'SchoolsCorner'])->name('schools-corner');
    Route::post('/schools-corner', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools-corner/{school:slug}', [SchoolController::class, 'show'])->name('schools.show');
    Route::get('/schools-corner/schools/{username}', [SchoolController::class, 'showUserSchools'])->name('schools.userschools');
    Route::get('/schools-corner/edit-school/{id}', [SchoolController::class, 'editSchool'])->name('schools-corner.edit');
    Route::put('/schools-corner/school/{id}', [SchoolController::class, 'update'])->name('schools-corner.update');
    Route::delete('/schools-corner/{id}', [SchoolController::class, 'deleteSchool'])->name('schools-corner.deleteSchool');


    Route::get('/tbooke-blueboard', [TbookeBlueboardController::class, 'tbookeBlueboard'])->name('tbooke-blueboard');
    Route::get('/tbooke-blueboard/create', [TbookeBlueboardController::class, 'create'])->name('blueboard.create');
    Route::post('/tbooke-blueboard/store', [TbookeBlueboardController::class, 'store'])->name('blueboard.store');
    Route::get('/tbooke-blueboard/{username}', [TbookeBlueboardController::class, 'userPosts'])->name('blueboard.userPosts');
    Route::delete('/blueboard/{id}/delete', [TbookeBlueboardController::class, 'destroy'])->name('blueboard.delete');
    
    Route::get('/tbooke-blueboard/{username}/edit/{id}', [TbookeBlueboardController::class, 'edit'])->name('blueboard.edit');
    Route::put('/tbooke-blueboard/{username}/edit/{id}', [TbookeBlueboardController::class, 'update'])->name('blueboard.update');



    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{username?}', [MessageController::class, 'index'])->name('messages.show');
    Route::get('/messages/create/{username}', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');


    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    Route::get('/admin-messages', [AdminMessageController::class, 'index'])->name('admin-messages.index');
    Route::get('admin-messages/{messageId?}', [AdminMessageController::class, 'index'])->name('admin-messages.show');
    Route::post('/messages/store', [AdminMessageController::class, 'store'])->name('admin-messages.store');
    Route::post('admin-messages/{message}/reply', [AdminMessageController::class, 'storeUserReply'])->name('admin-messages.reply');

    Route::get('/help-center', [HelpCenterController::class, 'index'])->name('help-center');

    // Group routes
    
    Route::get('/groups/my-groups', [GroupController::class, 'myGroups'])->name('groups.myGroups');
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index'); 
    Route::get('/groups/create', [GroupController::class, 'creategroup'])->name('group.create'); 
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store'); 
    Route::get('/groups/{slug}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group:slug}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::get('/groups/{slug}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::delete('/groups/{slug}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::put('/groups/{slug}', [GroupController::class, 'update'])->name('groups.update');
    
    
    // Group post routes
    Route::post('groups/{slug}/posts', [GroupPostController::class, 'store'])->name('group.posts.store');
    Route::post('groups/{post}/comment', [GroupController::class, 'storeComment'])->name('group.posts.comment');
    Route::post('groups/{post}/like', [GroupController::class, 'likePost'])->name('group.posts.like');
    Route::post('groups/{post}/repost', [GroupController::class, 'repostPost'])->name('group.posts.repost');


});

    // Admin routes
    Route::get('/admin-login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin-login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::post('/admin-logout', [AdminController::class, 'logout'])->name('admin.logout'); 

    Route::middleware([AdminAuth::class])->group(function () {
    Route::get('/admin-panel', [AdminController::class, 'index'])->name('admin.admin-panel');
    
    Route::get('/admin-panel/user-subjects', [SubjectController::class, 'userSubjects'])->name('user.subjects');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    Route::get('/admin-panel/user-topics', [TopicController::class, 'index'])->name('topics.index');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::put('/topics/{id}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/topics/{id}', [TopicController::class, 'destroy'])->name('topics.destroy');

    Route::get('/admin-panel/messages', [AdminMessageController::class, 'adminMessages'])->name('admin-messages.adminMessages');
    Route::get('/admin-panel/messages/{id}', [AdminMessageController::class, 'show'])->name('admin-panel.show');
    Route::post('/admin/messages/{id}/reply', [AdminMessageController::class, 'storeAdminReply'])->name('admin.messages.storeAdminReply');

    Route::get('/users', [AdminController::class, 'showUsers'])->name('admin-panel.users');

    Route::put('/admin/users/{id}/update', [AdminController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{id}/archive', [AdminController::class, 'archive'])->name('admin.users.archive');
    Route::post('/admin/users/{id}/unarchive', [AdminController::class, 'unarchiveUser'])->name('users.unarchive');

});

require __DIR__.'/auth.php';
