<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\AlumniController;

// Public Routes (Landing Page)
Route::get('/', [HomeController::class, 'index'])->name('home');
// Backward compatibility: support legacy /home URL by redirecting to root
Route::redirect('/home', '/');
// Backward-compatible route for named welcome links
Route::get('/welcome', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/announcements/{id}', [HomeController::class, 'showAnnouncement'])->name('announcements.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/programs', [HomeController::class, 'programs'])->name('programs');
Route::get('/teachers', [HomeController::class, 'teachers'])->name('teachers');
Route::get('/facilities', [HomeController::class, 'facilities'])->name('facilities');

// Student Application Routes (Public)
Route::get('/register-student', [StudentApplicationController::class, 'showRegistrationForm'])->name('student.register');
Route::post('/register-student', [StudentApplicationController::class, 'submitApplication'])->name('student.register.submit');
Route::get('/application-success', [StudentApplicationController::class, 'applicationSuccess'])->name('application.success');
Route::get('/check-application-status', [StudentApplicationController::class, 'showStatusForm'])->name('application.status');
Route::post('/check-application-status', [StudentApplicationController::class, 'checkStatus'])->name('application.check-status');

Auth::routes();

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function() {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/applications', [AdminController::class, 'applications'])->name('applications.index');
        Route::get('/applications/pending', [AdminController::class, 'pendingApplications'])->name('applications.pending');
        Route::get('/applications/{id}', [AdminController::class, 'applicationDetail'])->name('applications.detail');
        Route::post('/applications/{id}/approve', [AdminController::class, 'approveApplication'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [AdminController::class, 'rejectApplication'])->name('applications.reject');
        Route::get('/students', [AdminController::class, 'students'])->name('students.index');
        Route::get('/students/create', [AdminController::class, 'createStudentForm'])->name('students.create');
        Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
        Route::get('/students/{id}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
        Route::put('/students/{id}', [AdminController::class, 'updateStudent'])->name('students.update');
        Route::delete('/students/{id}', [AdminController::class, 'destroyStudent'])->name('students.destroy');
        Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers.index');
        Route::get('/teachers/create', [AdminController::class, 'createTeacherForm'])->name('teachers.create');
        Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
        Route::get('/teachers/{id}/edit', [AdminController::class, 'editTeacher'])->name('teachers.edit');
        Route::put('/teachers/{id}', [AdminController::class, 'updateTeacher'])->name('teachers.update');
        Route::delete('/teachers/{id}', [AdminController::class, 'destroyTeacher'])->name('teachers.destroy');
        Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects.index');
        Route::get('/subjects/create', [AdminController::class, 'createSubjectForm'])->name('subjects.create');
        Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('subjects.store');
        Route::get('/subjects/{id}/edit', [AdminController::class, 'editSubject'])->name('subjects.edit');
        Route::put('/subjects/{id}', [AdminController::class, 'updateSubject'])->name('subjects.update');
        Route::delete('/subjects/{id}', [AdminController::class, 'destroySubject'])->name('subjects.destroy');
        Route::get('/schedules', [AdminController::class, 'schedules'])->name('schedules.index');
        Route::get('/schedules/create', [AdminController::class, 'createScheduleForm'])->name('schedules.create');
        Route::post('/schedules', [AdminController::class, 'storeSchedule'])->name('schedules.store');
        Route::get('/schedules/{id}/edit', [AdminController::class, 'editSchedule'])->name('schedules.edit');
        Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{id}', [AdminController::class, 'destroySchedule'])->name('schedules.destroy');
        Route::patch('/schedules/{id}/toggle', [AdminController::class, 'toggleSchedule'])->name('schedules.toggle');
        Route::get('/classes', [AdminController::class, 'classes'])->name('classes.index');
        Route::get('/classes/create', [AdminController::class, 'createClassForm'])->name('classes.create');
        Route::post('/classes', [AdminController::class, 'storeClass'])->name('classes.store');
        Route::get('/classes/{id}/edit', [AdminController::class, 'editClass'])->name('classes.edit');
        Route::put('/classes/{id}', [AdminController::class, 'updateClass'])->name('classes.update');
        Route::delete('/classes/{id}', [AdminController::class, 'destroyClass'])->name('classes.destroy');
        Route::patch('/classes/{id}/toggle', [AdminController::class, 'toggleClass'])->name('classes.toggle');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUserForm'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements.index');
        Route::get('/announcements/create', [AdminController::class, 'createAnnouncementForm'])->name('announcements.create');
        Route::post('/announcements', [AdminController::class, 'createAnnouncement'])->name('announcements.store');
        Route::get('/announcements/{id}/edit', [AdminController::class, 'editAnnouncement'])->name('announcements.edit');
        Route::put('/announcements/{id}', [AdminController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{id}', [AdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/activities', [AdminController::class, 'activities'])->name('activities.index');
        Route::get('/alumni', [AdminController::class, 'alumni'])->name('alumni.index');
        Route::get('/alumni/create', [AdminController::class, 'alumniCreate'])->name('alumni.create');
        Route::post('/alumni', [AdminController::class, 'alumniStore'])->name('alumni.store');
        Route::get('/alumni/{id}/edit', [AdminController::class, 'alumniEdit'])->name('alumni.edit');
        Route::put('/alumni/{id}', [AdminController::class, 'alumniUpdate'])->name('alumni.update');
        Route::delete('/alumni/{id}', [AdminController::class, 'alumniDestroy'])->name('alumni.destroy');
        // School Info Management
        Route::get('/school/edit', [AdminController::class, 'editSchool'])->name('school.edit');
        Route::put('/school', [AdminController::class, 'updateSchool'])->name('school.update');
        // System Settings
        Route::get('/system/settings', [AdminController::class, 'systemSettings'])->name('system.settings');
        Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats'])->name('dashboard.stats');
    });

    // Student Routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile/create', [StudentController::class, 'create'])->name('profile.create');
        Route::post('/profile', [StudentController::class, 'store'])->name('profile.store');
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::get('/schedules', [StudentController::class, 'schedules'])->name('schedules');
        Route::get('/grades', [StudentController::class, 'grades'])->name('grades');
        // Announcements accessible by students
        Route::get('/announcements', [StudentController::class, 'announcements'])->name('announcements');
        Route::get('/materials', [StudentController::class, 'materials'])->name('materials');
    });

    // Teacher Routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/classes/{id}', [TeacherController::class, 'classDetail'])->name('class.detail');
        Route::get('/students/{id}', [TeacherController::class, 'studentDetail'])->name('students.detail');
        Route::post('/grades', [TeacherController::class, 'addGrade'])->name('grades.store');
        Route::put('/grades/{id}', [TeacherController::class, 'updateGrade'])->name('grades.update');
        Route::delete('/grades/{id}', [TeacherController::class, 'destroyGrade'])->name('grades.destroy');
        // Grade management UI (per-class/subject bulk inputs)
        Route::get('/grades/manage', [TeacherController::class, 'manageGrades'])->name('grades.manage');
        Route::post('/grades/manage', [TeacherController::class, 'storeBulkGrades'])->name('grades.manage.store');
        Route::post('/skills', [TeacherController::class, 'addSkill'])->name('skills.store');
        Route::put('/skills/{id}', [TeacherController::class, 'updateSkill'])->name('skills.update');
        Route::delete('/skills/{id}', [TeacherController::class, 'destroySkill'])->name('skills.destroy');
        // Teacher profile management
        Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [TeacherController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [TeacherController::class, 'updateProfile'])->name('profile.update');
        // Teacher announcement creation
        Route::get('/announcements/create', [TeacherController::class, 'createAnnouncementForm'])->name('announcements.create');
        Route::post('/announcements', [TeacherController::class, 'createAnnouncement'])->name('announcements.store');
        Route::get('/schedules', [TeacherController::class, 'schedules'])->name('schedules');
        // Teacher materials
        Route::get('/materials', [App\Http\Controllers\TeacherMaterialsController::class, 'index'])->name('materials.index');
        Route::get('/materials/create', [App\Http\Controllers\TeacherMaterialsController::class, 'create'])->name('materials.create');
        Route::post('/materials', [App\Http\Controllers\TeacherMaterialsController::class, 'store'])->name('materials.store');
        Route::get('/materials/{id}/edit', [App\Http\Controllers\TeacherMaterialsController::class, 'edit'])->name('materials.edit');
        Route::put('/materials/{id}', [App\Http\Controllers\TeacherMaterialsController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{id}', [App\Http\Controllers\TeacherMaterialsController::class, 'destroy'])->name('materials.destroy');
    });

    // Alumni Routes
    Route::middleware('role:student')->prefix('alumni')->name('alumni.')->group(function () {
        Route::get('/dashboard', [AlumniController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AlumniController::class, 'profile'])->name('profile');
        Route::put('/profile', [AlumniController::class, 'updateProfile'])->name('profile.update');
        Route::get('/training', [AlumniController::class, 'training'])->name('training');
    });
});

// Route for saving desired class from verify page - only for logged-in users
use App\Http\Controllers\Auth\VerificationController as AuthVerification;
Route::post('/verification/preference', [AuthVerification::class, 'updatePreference'])->name('verification.preference')->middleware('auth');
