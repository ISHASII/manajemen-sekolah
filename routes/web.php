<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\KejuruanController;

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

// Public student profile (for QR scans)
Route::get('/students/public/{id}', [\App\Http\Controllers\PublicStudentController::class, 'show'])->name('students.public');
Route::get('/students/public/{id}/qrcode', [\App\Http\Controllers\PublicStudentController::class, 'qrcode'])->name('students.public.qrcode');

Auth::routes();

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function() {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->role === 'kejuruan') {
            return redirect()->route('kejuruan.dashboard');
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
        // Manage kejuruan students (separate listing filtered by role)
        Route::get('/students/kejuruan', [AdminController::class, 'kejuruanStudents'])->name('students.kejuruan');
        Route::get('/students/create', [AdminController::class, 'createStudentForm'])->name('students.create');
        Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
        Route::get('/students/{id}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
        Route::get('/students/{id}/education-history', [AdminController::class, 'educationHistory'])->name('students.education-history');
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
        // AJAX route for getting student skills
        Route::get('/students/{studentId}/skills', [AdminController::class, 'getStudentSkills'])->name('students.skills');
        // AJAX route for getting student grade history
        Route::get('/students/grade-history/{historyId}', [AdminController::class, 'getStudentGradeHistory'])->name('students.grade-history');
        // Training classes for kejuruan students
        Route::get('/training-classes', [\App\Http\Controllers\Admin\TrainingClassController::class, 'index'])->name('training-classes.index');
        Route::get('/training-classes/create', [\App\Http\Controllers\Admin\TrainingClassController::class, 'create'])->name('training-classes.create');
        Route::post('/training-classes', [\App\Http\Controllers\Admin\TrainingClassController::class, 'store'])->name('training-classes.store');
        Route::get('/training-classes/{trainingClass}/edit', [\App\Http\Controllers\Admin\TrainingClassController::class, 'edit'])->name('training-classes.edit');
        Route::put('/training-classes/{trainingClass}', [\App\Http\Controllers\Admin\TrainingClassController::class, 'update'])->name('training-classes.update');
        Route::delete('/training-classes/{trainingClass}', [\App\Http\Controllers\Admin\TrainingClassController::class, 'destroy'])->name('training-classes.destroy');
        Route::get('/training-classes/{trainingClass}', [\App\Http\Controllers\Admin\TrainingClassController::class, 'show'])->name('training-classes.show');
        Route::post('/training-classes/{trainingClass}/add-participant', [\App\Http\Controllers\Admin\TrainingClassController::class, 'addParticipant'])->name('training-classes.add-participant');
        // Handle accidental GET to add-participant gracefully
        Route::get('/training-classes/{trainingClass}/add-participant', [\App\Http\Controllers\Admin\TrainingClassController::class, 'addParticipantRedirect']);
        Route::delete('/training-classes/{trainingClass}/remove-participant', [\App\Http\Controllers\Admin\TrainingClassController::class, 'removeParticipant'])->name('training-classes.remove-participant');
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
        Route::get('/grade-history', [StudentController::class, 'gradeHistory'])->name('grade-history');
        // Announcements accessible by students
        Route::get('/announcements', [StudentController::class, 'announcements'])->name('announcements');
        Route::get('/materials', [StudentController::class, 'materials'])->name('materials');
        // Attendance recap for logged-in student
        Route::get('/attendance', [StudentController::class, 'attendance'])->name('attendance');
        // Student submissions for materials
        Route::post('/materials/{material}/submissions', [\App\Http\Controllers\StudentSubmissionController::class, 'store'])->name('materials.submissions.store');
        Route::patch('/materials/{material}/submissions/{submission}', [\App\Http\Controllers\StudentSubmissionController::class, 'update'])->name('materials.submissions.update');
        // Training classes for students
        Route::get('/training-classes', [StudentController::class, 'trainingIndex'])->name('training-classes.index');
        Route::get('/training-classes/{id}', [StudentController::class, 'trainingShow'])->name('training-classes.show');
        Route::post('/training-classes/{id}/enroll', [StudentController::class, 'enrollTraining'])->name('training-classes.enroll');
        Route::delete('/training-classes/{id}/unenroll', [StudentController::class, 'unenrollTraining'])->name('training-classes.unenroll');
        // Student portfolios (for kejuruan students)
        Route::post('/portfolio', [\App\Http\Controllers\Student\PortfolioController::class, 'store'])->name('portfolio.store');
        Route::delete('/portfolio/{id}', [\App\Http\Controllers\Student\PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    });


    // Teacher Routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/classes/{id}', [TeacherController::class, 'classDetail'])->name('class.detail');
        Route::get('/students/{id}', [TeacherController::class, 'studentDetail'])->name('students.detail');
        // Per-student attendance recap (teacher view)
        Route::get('/students/{id}/attendance', [TeacherController::class, 'studentAttendanceReport'])->name('students.attendance');
        Route::post('/grades', [TeacherController::class, 'addGrade'])->name('grades.store');
        Route::put('/grades/{id}', [TeacherController::class, 'updateGrade'])->name('grades.update');
        Route::delete('/grades/{id}', [TeacherController::class, 'destroyGrade'])->name('grades.destroy');
        // Attendance
        Route::post('/attendances', [TeacherController::class, 'storeAttendance'])->name('attendance.store');
        // Bulk attendance (per-subject/day) for classes or training classes
        Route::post('/attendances/bulk', [TeacherController::class, 'storeBulkAttendance'])->name('attendance.bulk');
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
        Route::get('/schedules', [TeacherController::class, 'schedules'])->name('schedules');
        // Teacher materials
        Route::get('/class/{classId}/materials', [App\Http\Controllers\TeacherMaterialsController::class, 'classMaterials'])->name('class.materials');
        Route::get('/training-class/{trainingClassId}/materials', [App\Http\Controllers\TeacherMaterialsController::class, 'trainingMaterials'])->name('training-class.materials');
        Route::get('/training-classes/{id}', [TeacherController::class, 'trainingClassDetail'])->name('training-class.detail');
        Route::get('/classes/{id}/attendance', [TeacherController::class, 'classAttendanceReport'])->name('class.attendance');
        Route::get('/training-classes/{id}/attendance', [TeacherController::class, 'trainingAttendanceReport'])->name('training-class.attendance');
        Route::get('/materials', [App\Http\Controllers\TeacherMaterialsController::class, 'index'])->name('materials.index');
        Route::get('/attendance', [TeacherController::class, 'attendanceIndex'])->name('attendance.index');
        Route::get('/materials/create', [App\Http\Controllers\TeacherMaterialsController::class, 'create'])->name('materials.create');
        Route::post('/materials', [App\Http\Controllers\TeacherMaterialsController::class, 'store'])->name('materials.store');
        Route::get('/materials/{id}/edit', [App\Http\Controllers\TeacherMaterialsController::class, 'edit'])->name('materials.edit');
        Route::put('/materials/{id}', [App\Http\Controllers\TeacherMaterialsController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{id}', [App\Http\Controllers\TeacherMaterialsController::class, 'destroy'])->name('materials.destroy');
        // View student submissions for a specific material
        Route::get('/materials/{material}/submissions', [\App\Http\Controllers\StudentSubmissionController::class, 'index'])->name('materials.submissions.index');
        // Graduation management
        Route::get('/graduation', [TeacherController::class, 'graduationManagement'])->name('graduation');
        Route::post('/graduation/{studentId}', [TeacherController::class, 'processGraduation'])->name('graduation.process');
    });

    // Kejuruan Routes (Alumni yang sudah lulus)
    Route::middleware('kejuruan')->prefix('kejuruan')->name('kejuruan.')->group(function () {
        Route::get('/dashboard', [KejuruanController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [KejuruanController::class, 'profile'])->name('profile');
        Route::put('/profile', [KejuruanController::class, 'updateProfile'])->name('profile.update');
        Route::get('/schedules', [KejuruanController::class, 'schedules'])->name('schedules');
        Route::get('/grades', [KejuruanController::class, 'grades'])->name('grades');
        Route::get('/grade-history', [KejuruanController::class, 'gradeHistory'])->name('grade-history');
        Route::get('/announcements', [KejuruanController::class, 'announcements'])->name('announcements');
        Route::get('/materials', [KejuruanController::class, 'materials'])->name('materials');
        // Attendance recap for kejuruan student (self view)
        Route::get('/attendance', [KejuruanController::class, 'attendance'])->name('attendance');
        Route::post('/materials/{material}/submissions', [\App\Http\Controllers\StudentSubmissionController::class, 'store'])->name('materials.submissions.store');
        Route::patch('/materials/{material}/submissions/{submission}', [\App\Http\Controllers\StudentSubmissionController::class, 'update'])->name('materials.submissions.update');
        // Training classes only for kejuruan
        Route::get('/training-classes', [KejuruanController::class, 'trainingIndex'])->name('training-classes.index');
        Route::get('/training-classes/{id}', [KejuruanController::class, 'trainingShow'])->name('training-classes.show');
        Route::post('/training-classes/{id}/enroll', [KejuruanController::class, 'enrollTraining'])->name('training-classes.enroll');
        Route::delete('/training-classes/{id}/unenroll', [KejuruanController::class, 'unenrollTraining'])->name('training-classes.unenroll');
        Route::post('/portfolio', [\App\Http\Controllers\Student\PortfolioController::class, 'store'])->name('portfolio.store');
        Route::delete('/portfolio/{id}', [\App\Http\Controllers\Student\PortfolioController::class, 'destroy'])->name('portfolio.destroy');

        // CV Builder
        Route::get('/cv-builder', [\App\Http\Controllers\CVBuilderController::class, 'index'])->name('cv.index');
        Route::post('/cv-builder/preview', [\App\Http\Controllers\CVBuilderController::class, 'preview'])->name('cv.preview');
        Route::post('/cv-builder/generate', [\App\Http\Controllers\CVBuilderController::class, 'generate'])->name('cv.generate');
        Route::post('/cv-builder/print', [\App\Http\Controllers\CVBuilderController::class, 'print'])->name('cv.print');
    });
});

// Route for saving desired class from verify page - only for logged-in users
use App\Http\Controllers\Auth\VerificationController as AuthVerification;
Route::post('/verification/preference', [AuthVerification::class, 'updatePreference'])->name('verification.preference')->middleware('auth');
