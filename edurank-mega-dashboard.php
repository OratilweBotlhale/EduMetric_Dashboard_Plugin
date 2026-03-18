<?php
/**
 * Plugin Name: EduRank Mega Dashboard
 * Description: Creates School, Donor and Admin dashboards, leaderboard and impact stories pages & shortcodes for EduRank. Uses myCred / GiveWP if present. Safe fallbacks included.
 * Version: 1.0
 * Author: EduRank
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
   1) SHORTCODES: SCHOOL, DONOR, ADMIN, LEADERBOARD, IMPACT STORIES
   -------------------------------------------------------------------------*/

// ---------- SCHOOL DASHBOARD SHORTCODE ----------
add_shortcode('edurank_school_dashboard','edurank_render_school_dashboard');
function edurank_render_school_dashboard($atts){
    if(!is_user_logged_in()) {
        return '<div class="edurank-box"><p>Please <a href="'.esc_url(wp_login_url(get_permalink())).'">log in</a> to view your School Dashboard.</p></div>';
    }

    $user = wp_get_current_user();
    // Sample dynamic values: myCred balance, badges etc. (safe checks)
    $points = shortcode_exists('mycred_my_balance') ? do_shortcode('[mycred_my_balance]') : '';
    $badges = shortcode_exists('mycred_badges') ? do_shortcode('[mycred_badges]') : '';
    // user meta via Ultimate Member or general usermeta
    $total_students = esc_html( get_user_meta($user->ID, 'total_students', true) ?: '—' );
    $attendance = esc_html( get_user_meta($user->ID, 'attendance_rate', true) ?: '—' );
    $performance = esc_html( get_user_meta($user->ID, 'performance_score', true) ?: '—' );

    ob_start();
    ?>
    <div class="edurank-container">
        <div class="edurank-header">
            <div class="edurank-left">
                <h2>Welcome, <?php echo esc_html( $user->display_name ?: $user->user_login ); ?></h2>
                <p class="edurank-sub">School Dashboard — overview of points, badges and performance.</p>
            </div>
            <div class="edurank-right">
                <div class="edurank-small">Points</div>
                <div class="edurank-big"><?php echo $points ?: '<span style="opacity:.6">myCred not active</span>'; ?></div>
            </div>
        </div>

        <div class="edurank-grid">
            <div class="edurank-card">
                <div class="card-title">Total total_students</div>
                <div class="card-value"><?php echo $total_students; ?></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Attendance Rate</div>
                <div class="card-value"><?php echo $attendance; ?></div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo is_numeric($attendance)? intval($attendance).'%':'0%'; ?>"></div></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Performance Score</div>
                <div class="card-value"><?php echo $performance; ?></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Badges</div>
                <div class="card-value"><?php echo $badges ?: '<span style="opacity:.6">No badges yet</span>'; ?></div>
            </div>
        </div>

        <div class="edurank-actions">
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/school-performance') ); ?>">Update Performance</a>
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/school-activities') ); ?>">Post Activity</a>
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/impact-stories/submit') ); ?>">Submit Impact Story</a>
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/school-profile') ); ?>">Edit Profile</a>
        </div>

        <div class="edurank-section">
            <h3>Recent Updates</h3>
            <?php
            $loop = new WP_Query([
                'post_type' => ['attendance','activities','performance','post'],
                'posts_per_page' => 6,
                'author' => $user->ID,
                'post_status' => 'publish'
            ]);
            if($loop->have_posts()){
                echo '<ul class="edurank-list">';
                while($loop->have_posts()){ $loop->the_post();
                    echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a> <span class="muted">('.get_the_date().')</span></li>';
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                echo '<p>No recent updates yet. Use the buttons above to post updates.</p>';
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ---------- DONOR DASHBOARD SHORTCODE ----------
add_shortcode('edurank_donor_dashboard','edurank_render_donor_dashboard');
function edurank_render_donor_dashboard($atts){
    if(!is_user_logged_in()) {
        return '<div class="edurank-box"><p>Please <a href="'.esc_url(wp_login_url(get_permalink())).'">log in</a> to view donor features.</p></div>';
    }
    $user = wp_get_current_user();
    $mycred_balance = shortcode_exists('mycred_my_balance') ? do_shortcode('[mycred_my_balance]') : '';
    $donation_history = shortcode_exists('give_donation_history') ? do_shortcode('[give_donation_history number="8"]') : '<p>No GiveWP installed or no donations yet.</p>';
    // recommended (use myCred leaderboard or fallback to recent school profiles)
    $recommended = shortcode_exists('mycred_leaderboard') ? do_shortcode('[mycred_leaderboard number=5]') : edurank_recommended_fallback();

    ob_start();
    ?>
    <div class="edurank-container">
        <div class="edurank-header">
            <div class="edurank-left">
                <h2>Welcome, <?php echo esc_html( $user->display_name ?: $user->user_login ); ?></h2>
                <p class="edurank-sub">Donor Dashboard — track donations, mentorships and stories.</p>
            </div>
            <div class="edurank-right">
                <div class="edurank-small">Points</div>
                <div class="edurank-big"><?php echo $mycred_balance ?: '<span style="opacity:.6">myCred not active</span>'; ?></div>
            </div>
        </div>

        <div class="edurank-grid">
            <div class="edurank-card">
                <div class="card-title">Total Donations (platform)</div>
                <div class="card-value"><?php echo shortcode_exists('give_totals') ? do_shortcode('[give_totals]') : '—'; ?></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Schools Supported</div>
                <div class="card-value">—</div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Impact Stories Featured</div>
                <div class="card-value">—</div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Active Mentorships</div>
                <div class="card-value">—</div>
            </div>
        </div>

        <div class="edurank-section">
            <h3>Donation History</h3>
            <div class="edurank-donations"><?php echo $donation_history; ?></div>
        </div>

        <div class="edurank-section">
            <h3>Recommended Schools</h3>
            <div class="edurank-recommended"><?php echo $recommended; ?></div>
        </div>

        <div class="edurank-actions">
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/donate') ); ?>">Make a Donation</a>
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/impact-stories/submit') ); ?>">Submit an Impact Story</a>
            <a class="edurank-btn" href="<?php echo esc_url( site_url('/mentorship') ); ?>">Offer Mentorship</a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// helper fallback for recommended schools
function edurank_recommended_fallback() {
    $out = '<ul class="edurank-recommended-list">';
    $q = new WP_Query(['post_type'=>'school_profile','posts_per_page'=>5,'post_status'=>'publish']);
    if($q->have_posts()){
        while($q->have_posts()){ $q->the_post();
            $out .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
        }
        wp_reset_postdata();
    } else {
        $out .= '<li>No recommended schools yet</li>';
    }
    $out .= '</ul>';
    return $out;
}

// ---------- ADMIN DASHBOARD SHORTCODE ----------
add_shortcode('edurank_admin_dashboard','edurank_render_admin_dashboard');
function edurank_render_admin_dashboard($atts){
    if(!current_user_can('manage_options')) {
        return '<div class="edurank-box"><p>Access denied. Admins only.</p></div>';
    }

    // totals
    $total_posts = wp_count_posts();
    $total_donations = shortcode_exists('give_totals') ? do_shortcode('[give_totals]') : '—';
    ob_start();
    ?>
    <div class="edurank-container">
        <div class="edurank-header">
            <div class="edurank-left">
                <h2>Admin Dashboard</h2>
                <p class="edurank-sub">Platform overview & moderation tools.</p>
            </div>
        </div>

        <div class="edurank-grid">
            <div class="edurank-card">
                <div class="card-title">Total Schools</div>
                <div class="card-value"><?php echo edurank_count_users_by_role('school'); ?></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Total Donors</div>
                <div class="card-value"><?php echo edurank_count_users_by_role('donor'); ?></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Total Donations</div>
                <div class="card-value"><?php echo $total_donations; ?></div>
            </div>

            <div class="edurank-card">
                <div class="card-title">Pending Stories</div>
                <div class="card-value"><?php echo edurank_count_pending_stories(); ?></div>
            </div>
        </div>

        <div class="edurank-section">
            <h3>Top Schools (by myCred points)</h3>
            <?php
            if(shortcode_exists('mycred_leaderboard')) {
                echo do_shortcode('[mycred_leaderboard number=10]');
            } else {
                echo '<p>Install myCred to show leaderboards here.</p>';
            }
            ?>
        </div>

        <div class="edurank-section">
            <h3>Pending Impact Stories</h3>
            <?php
            $pending = new WP_Query(['post_type'=>'impact_story','post_status'=>'pending','posts_per_page'=>10]);
            if($pending->have_posts()){
                echo '<ul class="edurank-list">';
                while($pending->have_posts()){ $pending->the_post();
                    echo '<li><strong>'.get_the_title().'</strong> — <a href="'.get_edit_post_link().'">Review</a></li>';
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                echo '<p>No pending stories.</p>';
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// helper functions for admin shortcode
function edurank_count_users_by_role($role){
    if(!function_exists('get_users')) return '—';
    $users = get_users(['role'=>$role,'fields'=>'ID']);
    return is_array($users) ? count($users) : 0;
}
function edurank_count_pending_stories(){
    $q = new WP_Query(['post_type'=>'impact_story','post_status'=>'pending','posts_per_page'=>1]);
    $count = $q->found_posts ?: 0;
    wp_reset_postdata();
    return $count;
}

// ---------- LEADERBOARD SHORTCODE ----------
add_shortcode('edurank_leaderboard','edurank_render_leaderboard');
function edurank_render_leaderboard($atts){
    // If myCred is installed, use its leaderboard; otherwise show a fallback
    if(shortcode_exists('mycred_leaderboard')){
        return do_shortcode('[mycred_leaderboard number=20 wrap="div" template="<div class=\'lr-item\'>#%position% %user_profile_link% - %cred_f% pts</div>"]');
    } else {
        // fallback: show recent school profiles
        $q = new WP_Query(['post_type'=>'school_profile','posts_per_page'=>20,'post_status'=>'publish']);
        $out = '<div class="edurank-leaderboard">';
        if($q->have_posts()){
            $pos = 1;
            while($q->have_posts()){ $q->the_post();
                $out .= '<div class="lr-item">#'.$pos.' <a href="'.get_permalink().'">'.get_the_title().'</a></div>';
                $pos++;
            }
            wp_reset_postdata();
        } else {
            $out .= '<p>No schools yet.</p>';
        }
        $out .= '</div>';
        return $out;
    }
}

// ---------- IMPACT STORIES SHORTCODE ----------
add_shortcode('edurank_impact_stories','edurank_render_impact_stories');
function edurank_render_impact_stories($atts){
    $q = new WP_Query(['post_type'=>'impact_story','posts_per_page'=>10,'post_status'=>'publish']);
    $out = '<div class="edurank-impact-grid">';
    if($q->have_posts()){
        while($q->have_posts()){ $q->the_post();
            $thumb = get_the_post_thumbnail_url(get_the_ID(),'medium') ?: '';
            $out .= '<div class="impact-card">';
            if($thumb) $out .= '<div class="impact-thumb"><img src="'.esc_url($thumb).'" style="max-width:100%;border-radius:8px" alt="'.esc_attr(get_the_title()).'"/></div>';
            $out .= '<h4><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
            $out .= '<p class="muted">'.get_the_date().'</p>';
            $out .= '<p>'.wp_trim_words(get_the_excerpt(), 28).'</p>';
            $out .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $out .= '<p>No impact stories yet.</p>';
    }
    $out .= '</div>';
    return $out;
}

/* -------------------------------------------------------------------------
   2) STYLES (simple & safe)
   -------------------------------------------------------------------------*/
add_action('wp_head','edurank_global_styles');
function edurank_global_styles(){
    ?>
    <style>
    /* Basic EduRank dashboard styles (applies site-wide) */
    .edurank-container{ max-width:1200px; margin:18px auto; padding:0 18px; font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;}
    .edurank-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
    .edurank-left h2{ margin:0; font-size:24px; font-weight:700; }
    .edurank-sub{ color:#6b7280; margin:4px 0 0; }
    .edurank-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:18px; }
    .edurank-card{ background:#fff; border-radius:12px; padding:16px; box-shadow:0 6px 18px rgba(12,20,40,0.04); border:1px solid #f1f5f9; text-align:left; }
    .card-title{ color:#374151; font-weight:600; margin-bottom:6px; }
    .card-value{ font-size:20px; font-weight:700; }
    .edurank-actions{ display:flex; flex-wrap:wrap; gap:10px; margin:16px 0; }
    .edurank-btn{ display:inline-block; background:#0066ff; color:#fff; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:600; }
    .edurank-section{ margin-top:14px; }
    .edurank-list{ list-style:none; padding-left:0; margin:0; }
    .edurank-list li{ padding:8px 0; border-bottom:1px dashed #eef2ff; }
    .progress-bar{ width:100%; height:10px; background:#eef2ff; border-radius:999px; overflow:hidden; margin-top:8px; }
    .progress-fill{ height:100%; background:linear-gradient(90deg,#007bff,#00b4ff); border-radius:999px; }
    .edurank-leaderboard .lr-item{ padding:8px 0; border-bottom:1px solid #f3f4f6; }
    .edurank-impact-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:14px; }
    .impact-card{ background:#fff; padding:14px; border-radius:12px; box-shadow:0 6px 16px rgba(12,20,40,0.04); }
    .muted{ color:#6b7280; font-size:13px; }
    @media (max-width:700px){ .edurank-header{ flex-direction:column; align-items:flex-start } .edurank-grid{ grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); } .edurank-actions{ flex-direction:column; }}
    </style>
    <?php
}

/* -------------------------------------------------------------------------
   3) AUTO-CREATE PAGES ON PLUGIN ACTIVATION
   -------------------------------------------------------------------------*/
register_activation_hook(__FILE__,'edurank_create_pages_on_activation');
function edurank_create_pages_on_activation(){
    $pages = [
        'School Dashboard' => '[edurank_school_dashboard]',
        'Donor Dashboard' => '[edurank_donor_dashboard]',
        'Admin Dashboard' => '[edurank_admin_dashboard]',
        'Leaderboard' => '[edurank_leaderboard]',
        'Impact Stories' => '[edurank_impact_stories]'
    ];
    foreach($pages as $title => $content){
        $exists = get_page_by_title($title);
        if(!$exists){
            wp_insert_post([
                'post_title' => wp_strip_all_tags($title),
                'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => 'page'
            ]);
        }
    }
}

/* -------------------------------------------------------------------------
   4) OPTIONAL: uninstall cleanup (keeps it safe — not deleting user data)
   -------------------------------------------------------------------------*/
register_uninstall_hook(__FILE__,'edurank_uninstall_cleanup');
function edurank_uninstall_cleanup(){
    // This intentionally leaves user data & posts intact.
    // If you want the plugin to remove pages it created, uncomment the lines below:
    /*
    $titles = ['School Dashboard','Donor Dashboard','Admin Dashboard','Leaderboard','Impact Stories'];
    foreach($titles as $t){
        $p = get_page_by_title($t);
        if($p) wp_delete_post($p->ID,true);
    }
    */
}
