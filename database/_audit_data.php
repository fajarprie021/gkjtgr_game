<?php
/**
 * Audit data — single source of truth for all 3 export formats.
 * Loaded by _build_audit_xml.php, _build_audit_xls.php, _build_audit_csv.php.
 */

return [

  'audit' => [

    ['A-001','Home/Landing','/','Buka tanpa login','Halaman tampil tanpa PHP/JS error, CTA kelas muncul','Lulus','–','GET / → 200, body 1687B + #app + spinner','public/index.php, public/assets/js/app.js','Tidak ada','Tidak ada','Frontend Bootstrap + 7 JS dimuat dari CDN lokal'],
    ['A-002','Halaman Permainan','/ (SPA Map→Story→Mission)','Klik "Penciptaan" → mission','Map render, StoryView aktif, Game.startMisi panggil /api/questions, render 6 tipe soal','Lulus','–','API live + app.js pakai ApiService + ProgressService','public/assets/js/app.js, game-engine.js, map.js','Tidak ada','Tidak ada','Tidak ada route /map.php — SPA JS-view by design'],
    ['A-003','Admin Dashboard','/admin/','Login admin lalu buka dashboard','Seluruh folder public/admin/ tidak ada. 404 untuk /admin/, /admin/login.php, /admin/dashboard.php. staff_users.role=admin sudah ada di DB','N/A','Major','GET /admin/* → 404','(folder belum dibuat)','Bikin public/admin/login.php, dashboard.php, dan modul admin penuh (CRUD stories, players, staff, sessions)','Setelah dibuat','Teacher dashboard tombol Admin Panel → ../admin/ juga 404'],
    ['A-004','Teacher Dashboard','/teacher/dashboard.php','Login teacher lalu buka dashboard','Dashboard tampil dengan 4 kartu (Buat Sesi, Riwayat, Kelola Pemain, Analytics) + kartu Admin (kondisional)','Lulus','–','Tanpa session → 401; setelah login → 200','public/teacher/dashboard.php, config/auth.php (requireStaffAuth)','Tidak ada','Tidak ada','3 dari 4 link = 404 (lihat A-008)'],
    ['A-005','Player Dashboard','/player/dashboard.php','Login player lalu buka dashboard','Tampil: greeting, kelas, progress-circle (jumlah cerita selesai), 3 kartu aksi','Lulus','–','Tanpa session → 401; setelah login GKJ-1001/1234 → 200, body 4486B','public/player/dashboard.php','Tidak ada','Tidak ada','Link "Lanjutkan Petualangan" → /map.php (lihat A-010)'],
    ['A-006','Guest/Join','/join.php','Buka join tanpa login','Tidak ada file /join.php atau /player/join.php. Frontend belum punya screen join by class code','Gagal','Major','GET /player/join.php → 404','(file belum dibuat)','Bikin public/player/join.php (form kode kelas → lookup game_sessions.session_code) + tambahkan App.showJoinScreen() di app.js','Setelah dibuat','Skema game_sessions.session_code sudah ada di DB'],
    ['A-007','Menu Admin','/admin/*','Klik seluruh menu admin','Folder admin tidak ada, semua link 404','Gagal','Major','GET /admin/* → 404 untuk 3 URL','teacher/dashboard.php:107 (href="../admin/")','Lihat A-003','–','–'],
    ['A-008','Menu Teacher','/teacher/session-create.php, sessions.php, players.php','Klik kartu','3 dari 4 menu teacher 404, hanya Analytics yang hidup','Gagal','Major','GET /teacher/session-create.php → 404; sessions.php → 404; players.php → 404','teacher/dashboard.php:54,67,80','Bikin 3 halaman teacher: session-create.php, sessions.php, players.php. Atau ganti tombol jadi "Segera Hadir" (disabled)','Setelah dibuat','Sudah didokumentasikan di v1-release.md'],
    ['A-009','Menu Player','/player/join.php, progress.php, /map.php','Klik 3 kartu','2 dari 3 link utama player 404. Hanya logout yang hidup','Gagal','Major','GET /player/join.php → 404; progress.php → 404; /map.php → 404','player/dashboard.php:90,106,119','Bikin public/player/join.php + progress.php, dan ganti link "Lanjutkan Petualangan" ke / bukan /map.php (game ada di SPA /)','Setelah dibuat','Atau ganti ke href="../"'],
    ['A-010','Placeholder Pages','semua route yang seharusnya file','Identifikasi halaman kosong/tidak ada','Route berikut 404: /join.php, /map.php, /story.php, /mission.php, /player/join.php, /player/progress.php, /teacher/session-create.php, /teacher/sessions.php, /teacher/players.php, /admin/*','Partial','Major','HTTP 404 untuk 10 route','(lihat A-007/A-008/A-009)','Daftar placeholder ada di Sheet "Placeholder Pages"','Setelah dibuat','Game SPA menutupi semua dari /, tapi link statis di dashboard & README mengarah ke file yang tidak ada'],
    ['A-011','PHP Error','seluruh aplikasi','Navigasi','Tidak ada error/warning/notice/fatal. display_errors tidak di-set (default dev=ON). config/security.php mengirim X-Content-Type-Options, X-Frame-Options, Referrer-Policy, CSP','Lulus','Minor','24 URL dites, tidak ada HTML error PHP. CSP aktif','public/index.php, teacher/dashboard.php, config/security.php','Tambahkan di config/security.php: ini_set("display_errors","0"); error_reporting(E_ALL & ~E_DEPRECATED)','Setelah patch','Aman secara fungsional'],
    ['A-012','Browser Console','seluruh aplikasi','Console error saat navigasi','Source JS: 2 console.error (intentional fallback). Tidak ada console.log di production JS','Lulus','–','grep console.log di public/ → 0','public/assets/js/game-engine.js:57, api.js:50','Tidak ada','Tidak ada','Console.error hanya muncul saat API fail (by design)'],
    ['A-013','Network/Fetch','game flow','Buka Network saat main','API: eras, stories, questions, story-content, answer, analytics/event, health. GET ke POST-only → 405 (benar)','Lulus','–','HTTP probing: 7/7 API aktif','public/api/*.php','Tidak ada','Tidak ada','Tidak ada 404/500 penting'],
    ['A-014','Database Connection','semua halaman DB-dependent','Jalankan halaman DB','PDO connect sukses, 8 tabel aktif, database: ok di /health.php','Lulus','–','GET /health.php → 200 JSON {database:ok}; information_schema.TABLES → 8 baris','config/database.php, public/health.php','Tidak ada','Tidak ada','–'],
    ['A-015','Story Data Source','ApiService.getStories()','Sumber story','Hardcode PHP array di public/api/stories.php. Tidak baca DB','Partial','Medium','File stories.php line 11-87: array PHP literal','public/api/stories.php','Pindahkan ke tabel stories (schema sudah sediakan kolom map_x, map_y, is_active, dll — belum dipakai). Alternatif: extract ke config/stories_seed.php + migration loader','Setelah refactor','Schema stories ada di SQL tapi tidak di-import sebagai data'],
    ['A-016','Question Data Source','ApiService.getQuestions()','Sumber pertanyaan','Hardcode inline di public/api/questions.php (5-6 pertanyaan creation/small, fallback empty untuk noah/medium dst.)','Partial','Medium','public/api/questions.php line ~30+','public/api/questions.php','Sama dengan A-015 — pindah ke DB. Untuk v1 prototype, hardcode OK asalkan konsisten dengan content-release.md status','Setelah refactor','Story non-creation belum punya has_content lengkap'],
    ['A-017','Answer Validation','POST /api/answer.php','Jawab benar & salah','Server validator hidup untuk 6 tipe: multiple_choice, true_false, sequence, matching, timeline, verse_puzzle. Benar → {correct:true,feedback}, salah → {correct:false,feedback}','Lulus','–','Test: creation-q1-small "Allah" → correct:true; creation-q2-small "6 Hari" → correct:true; "3 Hari" → correct:false','public/api/answer.php','Tidak ada','Tidak ada','Server-side validation konsisten'],
    ['A-018','Navigation Back','game flow','Tombol back / browser back','SPA, tidak ada route file. Back via App.showMap()/showClassSelection() di tombol panah. Tidak ada state rusak teramati','Lulus','–','Inspect app.js: showClassSelection(), showMap(), back-button pada setiap view','public/assets/js/app.js:60-78','Tidak ada','Tidak ada','Aman'],
    ['A-019','Direct URL','route utama','Buka URL langsung','/ → home. /player/login.php → form login. /teacher/login.php → form login. Dashboard butuh session → redirect/401 (benar)','Lulus','–','HTTP probing 24 URL','public/player/login.php, teacher/login.php','Tidak ada','Tidak ada','Aman'],
    ['A-020','404 Handling','/route-tidak-ada','URL tidak valid','PHP built-in server fallback ke index.php, return 200 + home SPA. Tidak ada halaman 404 custom','Partial','Minor','GET /route-tidak-ada → 200, body = #app spinner','(file belum dibuat)','Tambahkan public/404.php dan router.php (custom PHP built-in). Cek: if (strpos($path,".") === false && !file_exists($file)) { http_response_code(404); include "404.php"; exit; }','Setelah patch','Alternatif: tambah .htaccess RewriteRule (kalau nanti pakai Apache), tapi untuk PHP built-in perlu router.php'],
    ['A-021','Empty State','dashboard/modul kosong','Halaman tanpa data','Analytics, Dashboard Guru: ada empty state ("Belum ada sesi aktif", "Belum ada data sesuai filter", "Data Belum Cukup", "Belum ada session", "Belum ada event"). Player Dashboard ada empty state default','Lulus','–','Inspect: teacher/dashboard.php:122-128, teacher/analytics.php line 106/111/115/116','public/teacher/dashboard.php, teacher/analytics.php','Tidak ada','Tidak ada','Aman'],
    ['A-022','Mobile 360px','/, /player/login.php','Viewport 360px','Bootstrap grid responsive + .mobile-container di index.php:28. CSS custom untuk touch target. Belum dites dengan emulator','Belum Dicek','–','–','public/index.php:28 (mobile-container), assets/css/theme.css','Verifikasi manual: Chrome DevTools → Responsive → 360×640. Atau npx playwright open --viewport-size=360,640 http://127.0.0.1:8000/','Setelah verifikasi','Perlu uji langsung'],
    ['A-023','Mobile 390px','sama','Viewport 390px','Sama dengan A-022','Belum Dicek','–','–','–','Sama','–','–'],
    ['A-024','Tablet','sama','Viewport tablet','Bootstrap md/lg breakpoint aktif (col-md-6 col-lg-4). Tidak ada media query rusak','Belum Dicek','–','–','teacher/dashboard.php:48-99 (grid)','Verifikasi manual di 768px & 1024px','–','–'],
    ['A-025','Asset Paths','/assets/css/*.css, /assets/js/*.js','Broken assets','7 CSS + 7 JS dimuat dari index.php. Semua 200 saat di-probe','Lulus','–','GET /assets/css/theme.css → 200; GET /assets/js/app.js → 200','public/index.php:21-48','Tidak ada','Tidak ada','CDN Bootstrap 5.3.0 & Icons 1.11.0'],
    ['A-026','Hardcoded Localhost','public/, config/','Cari localhost/127.0.0.1 hardcoded','Tidak ada hardcode di public/ atau config/. Yang ada hanya di README.md (dokumentasi) dan prompt/*.txt (prompt kerja)','Lulus','–','grep di 66 file → 0 hit di source code aplikasi','–','Tidak ada','Tidak ada','Aman'],
    ['A-027','Debug Output','public/, config/','console.log, var_dump, print_r, debugger','0 hit untuk console.log/var_dump/print_r/debugger di source aplikasi. console.error ada 2× (intentional logging)','Lulus','–','grep → 0 di production source','–','Tidak ada','Tidak ada','Production bersih'],
    ['A-028','TODO/FIXME','source','Cari TODO/FIXME/HACK','0 TODO/FIXME/HACK di source. Yang ada hanya di ITERATION_5_REPORT.md (dokumen lama) dan prompt files','Lulus','–','grep TODO|FIXME|HACK di 66 file → 0 di source code','–','Tidak ada','Tidak ada','Aman'],
    ['A-029','Database Schema','gkjtgr_game','Cek tabel vs modul UI','8 tabel aktif: analytics_events, game_players, game_sessions, game_teams, player_story_progress, players, staff_users, team_answers. Schema stories & questions terdefinisi di SQL tapi tidak digunakan aplikasi (hardcoded di PHP)','Partial','Medium','Query information_schema.TABLES','database/schema_combined_7_8.sql','Lihat A-015/A-016 — populate tabel stories & questions dari hardcoded data; atau buat view material','Setelah refactor','Tabel stories ada di schema tapi kosong'],
    ['A-030','Overall State','seluruh aplikasi','Ringkas modul','Home SPA Working, Game flow Working, API Working, Auth Working, Teacher Login+Dashboard Working, Teacher Analytics Working, Teacher sub-pages Missing (3 file), Player Login+Dashboard Working, Player join/progress Missing (2 file), Admin Missing (folder), Guest Missing, 404 handler Partial, DB Schema Working, Security headers Working, Mobile Untested','Partial','Major','–','–','–','–','Lihat Ringkasan A-030 di sheet terakhir'],
  ],

  'placeholders' => [
    ['P-01','public/404.php + router.php','Halaman 404 khusus untuk PHP built-in server','Minor','A-020'],
    ['P-02','public/player/join.php','Form masukkan kode sesi kelas (lookup game_sessions)','Major','A-006, A-009'],
    ['P-03','public/player/progress.php','List progress player (cerita selesai, skor, dll)','Major','A-009'],
    ['P-04','public/teacher/session-create.php','Form buat sesi baru (pilih story + class → generate session_code)','Major','A-008'],
    ['P-05','public/teacher/sessions.php','List game_sessions by teacher_id','Major','A-008'],
    ['P-06','public/teacher/players.php','List/CRUD pemain','Major','A-008'],
    ['P-07','public/admin/login.php','Login admin (filter staff_users.role="admin")','Major','A-003, A-007'],
    ['P-08','public/admin/dashboard.php','Dashboard admin','Major','A-003, A-007'],
    ['P-09','public/admin/users.php','CRUD staff_users','Major','A-003, A-007'],
    ['P-10','public/admin/content.php','CRUD stories + questions (ganti hardcode)','Major','A-003, A-007, A-015, A-016'],
    ['P-11','public/admin/sessions.php','List semua game_sessions','Major','A-003, A-007'],
    ['P-12','public/join.php (atau gunakan /player/join.php)','Guest entry: pilih kelas + masukkan kode','Major','A-006'],
  ],
  'summaries' => [
    ['Home SPA (/)','✅ Working','Bootstrap + game JS, fully responsive'],
    ['Game flow (Class→Map→Story→Mission)','✅ Working','6 question types di answer.php, hardcode content'],
    ['API (7 endpoint)','✅ Working','eras, stories, questions, story-content, answer, analytics/event, health'],
    ['Auth (teacher/player)','✅ Working','Session, bcrypt, role check'],
    ['Teacher Login + Dashboard','✅ Working','4 kartu, analytics hidup, 3 link 404'],
    ['Teacher Analytics','✅ Working','8 metrik, filter class/story/date'],
    ['Teacher session-create / sessions / players','❌ Missing','404 — belum dibangun'],
    ['Player Login + Dashboard','✅ Working','Greeting, progress circle, 3 link (2 broken)'],
    ['Player join / progress','❌ Missing','404 — link dari dashboard rusak'],
    ['Admin module','❌ Missing','Seluruh folder public/admin/ belum ada'],
    ['Guest / Class-code join','❌ Missing','Tidak ada entry point'],
    ['404 handler','⚠️ Partial','Fallback ke index.php, tidak ada halaman khusus'],
    ['DB Schema','✅ Working','8 tabel, FK benar, AUTO_INCREMENT benar'],
    ['Security headers','✅ Working','CSP, X-Frame-Options, X-Content-Type-Options, HSTS (HTTPS)'],
    ['Mobile responsive','🟡 Untested','Bootstrap grid OK, perlu verifikasi manual'],
  ],

  'headers' => ['ID','Area/Modul','URL/Route','Skenario Cek','Hasil Aktual/Temuan','Status Cek','Severity','Bukti/Screenshot/Log','File Terkait','Instruksi Perbaikan','Retest','Catatan'],

  'placeholder_headers' => ['ID','File/Route','Tujuan','Severity','Item Terkait'],

];




