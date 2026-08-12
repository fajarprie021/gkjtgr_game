<?php
/**
 * Test scenario master data for Bible Adventure v1.
 * Single source of truth for CSV/XLS exports.
 */

return [
    'headers' => ['ID','Level','Area','Precondition','Steps','Expected Result','Priority','Type','Status'],
    'scenarios' => [
        ['TS-001','Smoke','Landing/Home','Server running','Open /','Home SPA renders without PHP error; CTA buttons visible','High','Functional','Planned'],
        ['TS-002','Smoke','Login - Player','Valid player exists','Open /player/login.php and login with valid credentials','Player redirected to dashboard and session created','High','Functional','Planned'],
        ['TS-003','Smoke','Login - Teacher','Valid teacher exists','Open /teacher/login.php and login with valid credentials','Teacher redirected to dashboard and session created','High','Functional','Planned'],
        ['TS-004','End-to-End','Player Journey','Player logged in','From dashboard, continue to game/map flow','Player can continue to story/game flow without route error','High','Functional','Planned'],
        ['TS-005','End-to-End','Question Flow','Player in mission','Answer a multiple choice question','Server validates answer and returns correct feedback','High','Functional','Planned'],
        ['TS-006','End-to-End','Question Flow','Player in mission','Answer true/false question','Server validates answer and returns correct feedback','High','Functional','Planned'],
        ['TS-007','End-to-End','Question Flow','Player in mission','Answer sequence question','Server validates order and returns correct feedback','High','Functional','Planned'],
        ['TS-008','End-to-End','Question Flow','Player in mission','Answer matching question','Server validates pairs and returns correct feedback','High','Functional','Planned'],
        ['TS-009','End-to-End','Question Flow','Player in mission','Answer timeline question','Server validates chronology and returns correct feedback','High','Functional','Planned'],
        ['TS-010','End-to-End','Question Flow','Player in mission','Answer verse puzzle question','Server validates word order and returns correct feedback','High','Functional','Planned'],
        ['TS-011','Regression','API - Questions','Story exists','Call /api/questions.php?storyId=creation&classGroup=small','Returns questions array for the selected story/class','High','API','Planned'],
        ['TS-012','Regression','API - Questions','Invalid story','Call /api/questions.php with invalid storyId','Returns 404 and STORY_NOT_FOUND JSON','High','API','Planned'],
        ['TS-013','Regression','API - Answer','Valid question id','POST correct payload to /api/answer.php','Returns success=true and correct=true','High','API','Planned'],
        ['TS-014','Regression','API - Answer','Valid question id','POST wrong payload to /api/answer.php','Returns success=true and correct=false','High','API','Planned'],
        ['TS-015','Regression','API - Answer','Missing input','POST empty payload to /api/answer.php','Returns 400 INVALID_INPUT','Medium','API','Planned'],
        ['TS-016','Regression','Security','No session','Open teacher dashboard without login','Redirect or unauthorized response','High','Security','Planned'],
        ['TS-017','Regression','Security','No session','Open player dashboard without login','Redirect or unauthorized response','High','Security','Planned'],
        ['TS-018','Regression','Security Headers','Any page','Inspect response headers','Security headers present (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy)','Medium','Security','Planned'],
        ['TS-019','Regression','404 Handling','Invalid route','Open /route-tidak-ada','Custom 404 or graceful fallback is shown consistently','Medium','Navigation','Planned'],
        ['TS-020','Regression','Responsive','Desktop + mobile','Test 360px / 390px / tablet viewport','Layout remains usable and content readable','Medium','UI/Responsive','Planned'],
        ['TS-021','Regression','Teacher Dashboard','Teacher logged in','Open dashboard and inspect cards','Teacher sees dashboard cards and analytics access','High','Functional','Planned'],
        ['TS-022','Regression','Teacher Analytics','Teacher logged in','Open analytics page','Analytics page loads with charts / metrics / empty states','High','Functional','Planned'],
        ['TS-023','Regression','Logout','Logged in user','Click logout','Session is cleared and user is returned to login/home','Medium','Functional','Planned'],
        ['TS-024','Regression','Assets','Any page','Load CSS/JS assets','All required assets return 200 without mixed-content or missing file errors','Medium','Asset','Planned'],
        ['TS-025','Regression','Console/Client JS','Any page','Open browser console and navigate main flow','No unexpected client-side errors in production flow','Medium','Client','Planned'],
    ],
    'summary' => [
        ['Total Scenarios', 25],
        ['Smoke', 3],
        ['End-to-End', 7],
        ['Regression', 13],
        ['High Priority', 12],
        ['Medium Priority', 13],
    ],
];
