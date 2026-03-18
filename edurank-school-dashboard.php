<?php
/**
 * Plugin Name: EduRank – School Dashboard
 * Description: Auto-builds a styled school dashboard with stats, points, actions, and updates.
 * Version: 1.0
 * Author: EduRank
 */

if (!defined('ABSPATH')) exit;

/*--------------------------------------------------------------
    1. REGISTER SHORTCODE FOR THE SCHOOL DASHBOARD
--------------------------------------------------------------*/
add_shortcode('edurank_school_dashboard', 'edurank_render_school_dashboard');

function edurank_render_school_dashboard() {
    $school_name = wp_get_current_user()->display_name;
    $points = do_shortcode('[mycred_my_balance]');
    
    ob_start();
    ?>

    <div class="edurank-dashboard">

        <!-- HEADER -->
        <div class="edurank-header">
            <h2>Welcome, <?php echo esc_html($school_name); ?></h2>
            <p>Your School Performance Overview</p>
        </div>

        <!-- STATS CARDS -->
        <div class="edurank-stats-grid">

            <div class="edurank-card">
                <div class="icon">🏅</div>
                <h3>Total Points</h3>
                <p class="value"><?php echo $points; ?></p>
            </div>

            <div class="edurank-card">
                <div class="icon">📘</div>
                <h3>Attendance Updates</h3>
                <p class="value">
                    <?php echo wp_count_posts('attendance')->publish ?? 0; ?>
                </p>
            </div>

            <div class="edurank-card">
                <div class="icon">🎨</div>
                <h3>Activities Posted</h3>
                <p class="value">
                    <?php echo wp_count_posts('activities')->publish ?? 0; ?>
                </p>
            </div>

            <div class="edurank-card">
                <div class="icon">📈</div>
                <h3>Performance Updates</h3>
                <p class="value">
                    <?php echo wp_count_posts('performance')->publish ?? 0; ?>
                </p>
            </div>

        </div>

        <!-- QUICK ACTIONS -->
        <div class="edurank-actions">
            <a href="#" class="edurank-btn">Update Attendance</a>
            <a href="#" class="edurank-btn">Post Activity Update</a>
            <a href="#" class="edurank-btn">Add Student Achievement</a>
            <a href="#" class="edurank-btn">Upload Impact Story</a>
            <a href="#" class="edurank-btn">Edit School Profile</a>
            <a href="#" class="edurank-btn">View Badges</a>
        </div>

        <!-- CHART PLACEHOLDER -->
        <div class="edurank-chart">
            <h3>School Progress Chart</h3>
            <p>This will show school statistics in a chart. (Elementor or chart JS can go here.)</p>
        </div>

        <!-- LATEST UPDATES -->
        <div class="edurank-latest">
            <h3>Recent Updates</h3>
            <?php
                $loop = new WP_Query([
                    'post_type' => ['attendance', 'activities', 'performance'],
                    'posts_per_page' => 5
                ]);

                if ($loop->have_posts()):
                    echo "<ul>";
                    while ($loop->have_posts()): $loop->the_post();
                        echo "<li><strong>".get_the_title()."</strong> — ".get_the_date()."</li>";
                    endwhile;
                    echo "</ul>";
                else:
                    echo "<p>No updates yet.</p>";
                endif;
                wp_reset_postdata();
            ?>
        </div>

    </div>

    <?php
    return ob_get_clean();
}

/*--------------------------------------------------------------
    2. LOAD CSS FOR DASHBOARD DESIGN
--------------------------------------------------------------*/
add_action('wp_head', 'edurank_dashboard_styles');

function edurank_dashboard_styles() { ?>
    <style>
        .edurank-dashboard {
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }
        .edurank-header {
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            text-align: center;
        }
        .edurank-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }
        .edurank-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        .edurank-card .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .edurank-card .value {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
        }
        .edurank-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .edurank-btn {
            display: block;
            text-align: center;
            background: #0066ff;
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .edurank-btn:hover {
            background: #004ecc;
        }
        .edurank-chart, .edurank-latest {
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
    </style>
<?php }

/*--------------------------------------------------------------
    3. AUTO-CREATE DASHBOARD PAGE ON INSTALL
--------------------------------------------------------------*/
register_activation_hook(__FILE__, 'edurank_create_dashboard_page');

function edurank_create_dashboard_page() {
    $page_check = get_page_by_title('School Dashboard');
    if (!$page_check) {
        wp_insert_post([
            'post_title'    => 'School Dashboard',
            'post_content'  => '[edurank_school_dashboard]',
            'post_status'   => 'publish',
            'post_type'     => 'page'
        ]);
    }
}
