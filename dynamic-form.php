<?php

/**
 * Plugin Name: Dynamic form
 * Author: VIAZI
 * Version: 1.0
 * Text Domain: shamma
 * Domain Path: /languages
 * Description: Dynamic form for all persons who want to immigrate in other country
 */
defined('ABSPATH') or die('Arrête le banditisme fais ta part !');

//remove the button add new article to the body of dynamic_form
function remove_add_new_button_dynamic_form()
{
    global $post_type;

    if ($post_type == 'dynamic_form' && current_user_can('manage_options')) {
        echo '<style type="text/css">
            .wrap .page-title-action { display: none; }
        </style>';
    }
}

add_action('admin_head', 'remove_add_new_button_dynamic_form');

//on install plugin
function dynamic_form_install()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'dynamic_form_table';
    $table_name1 = $wpdb->prefix . 'dynamic_form_spouse_table';
    $table_name2 = $wpdb->prefix . 'dynamic_form_hosting_settings';


    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name && $wpdb->get_var("SHOW TABLES LIKE '$table_name1'") != $table_name1 && $wpdb->get_var("SHOW TABLES LIKE '$table_name2'") != $table_name2) {

        $charset_collate = $wpdb->get_charset_collate();

        $sql1 = "CREATE TABLE IF NOT EXISTS $table_name1 (
            spouse_id mediumint(9) NOT NULL AUTO_INCREMENT,
            spouse_name varchar(255) NOT NULL,
            spouse_age int NOT NULL,
            spouse_school_level varchar(255) NOT NULL,
            spouse_notes varchar(255) NOT NULL,
            spouse_experience_years int NOT NULL,
            spouse_work_domain varchar(255) NOT NULL,
            spouse_family_in_canada varchar(10),
            PRIMARY KEY  (spouse_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            nom varchar(255) NOT NULL,
            mail Varchar(255) NOT NULL,
            age int NOT NULL,
            school_level varchar(255) NOT NULL,
            notes varchar(255) NOT NULL,
            experience_years int NOT NULL,
            work_domain varchar(255) NOT NULL,
            children varchar(10) NOT NULL,
            children_details varchar(255),
            family_in_canada varchar(10),
            marital_status varchar(255) NOT NULL,
            spouse_id mediumint(9),
            PRIMARY KEY  (id),
            CONSTRAINT fk_spouse_id FOREIGN KEY (spouse_id) REFERENCES $table_name1(spouse_id)
        ) $charset_collate;";

        dbDelta($sql);


        $sql2 = "CREATE TABLE IF NOT EXISTS $table_name2 (
            settings_id mediumint(9) NOT NULL AUTO_INCREMENT,
            host_name varchar(255) NOT NULL,
            username  varchar(255) NOT NULL,
            host_password Varchar(255) NOT NULL,
            PRIMARY KEY  (settings_id)
        ) $charset_collate;";

        dbDelta($sql2);
    }
}

// register the dynamic_form_install when I install my plugin
register_activation_hook(__FILE__, 'dynamic_form_install');
// 

// display form shortcode
function display_form()
{
    $form_data = get_submit_form_data();
    $other_form_data = get_submit_spouse_form_data();
    $other_data = $other_form_data['other_data'];
    $user_data = $form_data['user_data'];
    $errors = $form_data['errors'];

    $form_success = isset($_GET['form_success']) && $_GET['form_success'] === 'true';

    // Réinitialisez les données du formulaire si nécessaire
    $user_data = $form_success ? [] : get_submit_form_data()['user_data'];
    $other_data = $form_success ? [] : get_submit_spouse_form_data()['other_data'];
    $errors = $form_success ? [] : get_submit_form_data()['errors'];

?>
    <div class="container-form">
        <form action="" method="post" autocomplete="off">
            <div class="name">
                <label for="nom"> <?= __("nom & prénoms", "shamma") ?> <sup>*</sup> : </label>
                <input type="text" name="nom" class="<?= isset($errors['nom']) ? 'error' : ''; ?>" value="<?= isset($user_data['nom']) ? esc_attr($user_data['nom']) : ''; ?>">
            </div>
            <div class="email">
                <label for="mail"> <?= __("Email", "shamma") ?> <sup>*</sup> : </label>
                <input type="email" name="mail" class="<?= isset($errors['mail']) ? 'error' : ''; ?>" value="<?= isset($user_data['mail']) ? esc_attr($user_data['mail']) : ''; ?>">
            </div>
            <div class="year">
                <label for="age"> <?= __("Âge", "shamma") ?> <sup>*</sup> : </label>
                <input type="number" name="age" min="0" class="<?= isset($errors['age']) ? 'error' : ''; ?>" value="<?= isset($user_data['age']) ? esc_attr($user_data['age']) : ''; ?>">
            </div>
            <div class="school_level">
                <label for="school_level"> <?= __("Niveau scolaire", "shamma") ?> <sup>*</sup> :</label>
                <input type="text" name="school_level" class="<?= isset($errors['school_level']) ? 'error' : ''; ?>" value="<?= isset($user_data['school_level']) ? esc_attr($user_data['school_level']) : ''; ?>">
            </div>
            <div class="notes">
                <label for="notes"> <?= __("Avez-vous tous vos relevés de note ? :", "shamma") ?></label>
                <input type="radio" name="notes" value="oui">
                <label for="notes"> <?= __("Oui", "shamma") ?></label>
                <input type="radio" name="notes" value="non">
                <label for="notes"> <?= __("Non", "shamma") ?></label>
                <?php if (isset($errors['notes'])) : ?>
                    <span class="error-message"><?= $errors['notes']; ?></span>
                <?php endif; ?>
            </div>
            <div class="experience_years">
                <label for="experience_years"><?= __("nombre d'année d'expérience professionnelle", "shamma") ?> <sup>*</sup> :</label>
                <input type="number" name="experience_years" min="0" class="<?= isset($errors['experience_years']) ? 'error' : ''; ?>" value="<?= isset($user_data['experience_years']) ? esc_attr($user_data['experience_years']) : ''; ?>">
            </div>
            <div class="work_domain">
                <label for="work_domain"><?= __("Domaine de travail", "shamma") ?> <sup>*</sup> : </label>
                <input type="text" name="work_domain" class="<?= isset($errors['work_domain']) ? 'error' : ''; ?>" value="<?= isset($user_data['work_domain']) ? esc_attr($user_data['work_domain']) : ''; ?>">
            </div>
            <div class="childrens">
                <label for="children"> <?= __("Avez-vous des enfants ? :", "shamma") ?></label>
                <input type="radio" name="children" class="children" value="oui">
                <label for="children"><?= __("Oui", "shamma") ?></label>
                <input type="radio" name="children" class="children" value="non">
                <label for="children"><?= __("Non", "shamma") ?></label> <br>
                <label for="children_details" class="children_details"> <?= __("Si oui combien d'enfants et quel âge ? :", "shamma") ?></label>
                <?php
                if (isset($errors['children_details'])) {
                    $error_class = 'children_details error';
                ?>
                    <style>
                        .children_details {
                            display: inline-block;
                        }
                    </style>
                    <script>
                        let eltradio = document.querySelector('.children');
                        eltradio.checked = true;
                    </script>
                <?php
                } else {
                    $error_class = 'children_details';
                }
                ?>
                <input type="text" name="children_details" id="empty-val" class="<?= $error_class; ?>">

            </div>
            <div class="family_in_canada">
                <label for="family_in_canada"><?= __("Avez-vous un frère ou une sœur direct au Canada ? :", "shamma") ?></label>
                <input type="radio" name="family_in_canada" value="Oui">
                <label for="children"><?= __("Oui", "shamma") ?></label>
                <input type="radio" name="family_in_canada" value="Non">
                <label for="children"><?= __("Non", "shamma") ?></label>
            </div>
            <div class="Mstatus">
                <label for="marital_status"> <?= __("Êtes-vous marié ? :", "shamma") ?></label>
                <input type="radio" name="marital_status" class="marital_status" value="oui">
                <label for="marital_status"><?= __("Oui", "shamma") ?></label>
                <input type="radio" name="marital_status" class="marital_status" value="non">
                <label for="marital_status"><?= __("Non", "shamma") ?></label>
                <?php if (isset($user_data['marital_status']) && $user_data['marital_status'] == 'oui') : ?>
                    <style>
                        .response-about-status {
                            display: inline-block;
                        }
                    </style>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            let maritalStatusRadio = document.querySelectorAll('.marital_status');
                            console.log("les radios du status du marié :", maritalStatusRadio)
                            maritalStatusRadio.forEach(function(e) {
                                if (e.value === 'oui') {
                                    console.log("verifcation 1 :", e.value === 'oui')
                                    e.checked = true;
                                }
                            })
                        });
                    </script>
                <?php else : ?>
                    <style>
                        .response-about-status {
                            display: none;
                        }
                    </style>
                <?php endif; ?>
            </div>
            <div class="response-about-status">
                <div class="Rmarital_status">
                    <label for="Rmarital_status"> <?= __("Si oui alors remplissez les informations relatives à votre conjoint ", "shamma") ?></label>
                </div>
                <div class="spouse_name">
                    <label for="spouse_name"> <?= __("nom & prénoms", "shamma") ?><sup>*</sup> : </label>
                    <input type="text" name="spouse_name" class="<?= isset($errors['spouse_name']) ? 'error' : ''; ?>" value="<?= isset($other_data['spouse_name']) ? esc_attr($other_data['spouse_name']) : ''; ?>">
                </div>
                <div class="spouse_age">
                    <label for="spouse_age"> <?= __("Âge", "shamma") ?> <sup>*</sup> : </label>
                    <input type="number" name="spouse_age" min="0" class="<?= isset($errors['spouse_age']) ? 'error' : ''; ?>" value="<?= isset($other_data['spouse_age']) ? esc_attr($other_data['spouse_age']) : ''; ?>">
                    <span>ans</span>
                </div>
                <div class="spouse_school_level">
                    <label for="spouse_school_level"> <?= __("Niveau scolaire", "shamma") ?> <sup>*</sup> :</label>
                    <input type="text" name="spouse_school_level" class="<?= isset($errors['spouse_school_level']) ? 'error' : ''; ?>" value="<?= isset($other_data['spouse_school_level']) ? esc_attr($other_data['spouse_school_level']) : ''; ?>">
                </div>
                <div class="spouse_notes">
                    <label for="spouse_notes"> <?= __("Avez-vous tous vos relevés de note ? :", "shamma") ?></label>
                    <input type="radio" name="spouse_notes" value="oui">
                    <label for="spouse_notes"> <?= __("Oui", "shamma") ?></label>
                    <input type="radio" name="spouse_notes" value="non">
                    <label for="spouse_notes"> <?= __("Non", "shamma") ?></label>
                    <?php if (isset($errors['spouse_notes'])) : ?>
                        <span class="error-message"><?= $errors['spouse_notes']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="spouse_experience_years">
                    <label for="spouse_experience_years"><?= __("nombre d'année d'expérience professionnelle", "shamma") ?><sup>*</sup> :</label>
                    <input type="number" name="spouse_experience_years" min="0" class="<?= isset($errors['spouse_experience_years']) ? 'error' : ''; ?>" value="<?= isset($other_data['spouse_experience_years']) ? esc_attr($other_data['spouse_experience_years']) : ''; ?>">
                </div>
                <div class="spouse_work_domain">
                    <label for="spouse_work_domain"><?= __("Domaine de travail", "shamma") ?> <sup>*</sup> : </label>
                    <input type="text" name="spouse_work_domain" class="<?= isset($errors['spouse_work_domain']) ? 'error' : ''; ?>" value="<?= isset($other_data['spouse_work_domain']) ? esc_attr($other_data['spouse_work_domain']) : ''; ?>">
                </div>
                <div class="spouse_family_in_canada">
                    <label for="spouse_family_in_canada"><?= __("Avez-vous un frère ou une sœur direct au Canada ? :", "shamma") ?></label>
                    <input type="radio" name="spouse_family_in_canada" value="Oui" class="<?= isset($errors['spouse_family_in_canada']) ? 'error' : ''; ?>">
                    <label for="spouse_family_in_canada"><?= __("Oui", "shamma") ?></label>
                    <input type="radio" name="spouse_family_in_canada" value="Non" class="<?= isset($errors['spouse_family_in_canada']) ? 'error' : ''; ?>">
                    <label for="spouse_family_in_canada"><?= __("Non", "shamma") ?></label>
                    <?php if (isset($errors['spouse_family_in_canada'])) : ?>
                        <span class="error-message"><?= $errors['spouse_family_in_canada']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="btn-submit">
                <button type="submit" name="submit-dynamic-form"><?= __("Soumettre", "shamma") ?></button>
            </div>
        </form>
    </div>
<?php
}
add_shortcode('dynamic_form', 'display_form');


//get informations of users during a submission form

function get_submit_form_data()
{
    $errors = [];
    $user_data = [];
    $error_message = __('Veuillez remplir ce champ', 'shamma');

    if (isset($_POST['submit-dynamic-form'])) {
        // fetch fields of form
        $fields = [
            "nom" => [
                "required" => true,
                "default" => "",
            ],
            "mail" => [
                "required" => true,
                "default" => "",
            ],
            "age" => [
                "required" => true,
                "default" => "",
            ],
            "school_level" => [
                "required" => true,
                "default" => "",
            ],
            "notes" => [
                "required" => true,
                "default" => "non",
            ],
            "experience_years" => [
                "required" => true,
                "default" => "",
            ],
            "work_domain" => [
                "required" => true,
                "default" => "",
            ],
            "children" => [
                "required" => true,
                "default" => "",
            ],
            "family_in_canada" => [
                "required" => true,
                "default" => "non",
            ],
            "marital_status" => [
                "required" => true,
                "default" => "",
            ],
        ];

        // Validation fields
        foreach ($fields as $key => $value) {
            if ($value["required"] && (!isset($_POST[$key]) || !$_POST[$key])) {
                if ($value["default"]) {
                    $user_data[$key] = $value["default"];
                } else {
                    $errors[$key] = $error_message;
                }
            } else if ($value["required"] && isset($_POST[$key]) && $_POST[$key]) {
                if (array_key_exists('marital_status', $_POST) && ($_POST['marital_status'] == 'oui')) {
                    $spouse_form = get_submit_spouse_form_data();
                    if (!empty($spouse_form['errors'])) {
                        $errors = array_merge($errors, $spouse_form['errors']);
                        var_dump("Les erreurs du tableau final :", $errors);
                    }
                }
                $user_data[$key] = sanitize_text_field($_POST[$key]);
            } else if (!$value["required"] && isset($_POST[$key]) && $_POST[$key]) {
                $user_data[$key] = sanitize_text_field($_POST[$key]);
            } else if (!$value["required"] && $value["default"]) {
                $user_data[$key] = $value["default"];
            }
        }

        if ($user_data["children"] === 'oui' && (!isset($_POST["children_details"]) || !$_POST["children_details"])) {
            $errors['children_details'] = $error_message;
        } else if ($user_data["children"] === 'oui') {
            $user_data["children_details"] = sanitize_text_field($_POST["children_details"]);
        } else {
            $user_data["children_details"] = "";
        }
    }
    return ['user_data' => $user_data, 'errors' => $errors];
}



//get a spouse form data


function get_submit_spouse_form_data()
{
    $errors = [];
    $other_data = [];
    $error_message = __('Veuillez remplir ce champ', 'shamma');

    if (isset($_POST['submit-dynamic-form'])) {

        //fetch spouse fields
        $fields = [
            "spouse_name" => [
                "required" => true,
                "default" => "",
            ],
            "spouse_age" => [
                "required" => true,
                "default" => "",
            ],
            "spouse_school_level" => [
                "required" => true,
                "default" => "",
            ],
            "spouse_notes" => [
                "required" => true,
                "default" => "non",
            ],
            "spouse_experience_years" => [
                "required" => true,
                "default" => "",
            ],
            "spouse_work_domain" => [
                "required" => true,
                "default" => "",
            ],
            "spouse_family_in_canada" => [
                "required" => true,
                "default" => "non",
            ],
        ];
        //validation fields
        foreach ($fields as $key => $value) {
            if ($value["required"] && (!isset($_POST[$key]) || !$_POST[$key])) {
                if ($value["default"]) {
                    $other_data[$key] = $value["default"];
                } else {
                    $errors[$key] = $error_message;
                }
            } else if ($value["required"] && isset($_POST[$key]) && $_POST[$key]) {
                $other_data[$key] = sanitize_text_field($_POST[$key]);
            } else if (!$value["required"] && isset($_POST[$key]) && $_POST[$key]) {
                $other_data[$key] = sanitize_text_field($_POST[$key]);
            } else if (!$value["required"] && $value["default"]) {
                $other_data[$key] = $value["default"];
            }
        }
    }

    return ['other_data' => $other_data, 'errors' => $errors];
}
// Fonction pour insérer les données du conjoint
function insert_spouse_data($other_data)
{
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'dynamic_form_spouse_table';

    try {
        // Insert datas
        $wpdb->insert(
            $table_name1,
            $other_data,
            [
                '%s', // spouse_name
                '%d', // spouse_age
                '%s', // spouse_school_level
                '%s', // spouse_notes
                '%d', // spouse_experience_years
                '%s', // spouse_work_domain
                '%s', // spouse_family_in_canada
            ]
        );
        return $wpdb->insert_id;
    } catch (Exception $e) {
        echo __('Une erreur s\'est produite lors de l\'insertion des données de l\'utilisateur : ', 'shamma') . $e->getMessage();
        return false;
    }
}

function insert_user_data($spouse_id, $user_data)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'dynamic_form_table';
    $user_data['spouse_id'] = $spouse_id;

    try {
        // Insert datas
        $result = $wpdb->insert(
            $table_name,
            $user_data,
            [
                '%s', // name
                '%s', //mail
                '%d', // age
                '%s', // school_level
                '%s', // notes
                '%d', // experience_years
                '%s', // work_domain
                '%s', // children
                '%s', // children_details
                '%s', // family_in_canada
                '%s'  // marital_status
            ]
        );
        if (!$result) {
            throw new Exception(__('Erreur lors de l\'insertion des données de l\'utilisateur.', 'shamma'));
        }
    } catch (Exception $e) {
        echo __('Une erreur s\'est produite lors de l\'insertion des données de l\'utilisateur : ', 'shamma') . $e->getMessage();
    }
}

if (isset($_POST['submit-dynamic-form'])) {
    // Récupérer les données de l'utilisateur
    $user_form_data = get_submit_form_data();
    $user_data = $user_form_data['user_data'];
    $user_errors = $user_form_data['errors'];

    // Récupérer les données du conjoint
    $spouse_form_data = get_submit_spouse_form_data();
    $spouse_data = $spouse_form_data['other_data'];
    $spouse_errors = $spouse_form_data['errors'];

    if (empty($user_errors)) {
        if (!empty($spouse_data) && empty($spouse_errors)) {
            $spouse_id = insert_spouse_data($spouse_data);
            $user_data['spouse_id'] = $spouse_id;
        }
        insert_user_data(isset($spouse_id) ? $spouse_id : null, $user_data);
    }
}

//display settings form

function display_dynamic_form_settings()
{
    $form_setting_data = get_settings_fields();
    $settings_data = $form_setting_data['settings_data'];
    $errors = $form_setting_data['errors'];
?>
    <div class="wrap">
        <h2><?php _e('Ajuster les paramètres SMTP de l\'hébergeur', 'shamma'); ?></h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="host_name"><?php _e('Nom de l\'hébergeur :', 'shamma'); ?></label></th>
                    <td><input type="text" id="host_name" name="host_name" class="<?= isset($errors['host_name']) ? 'error' : ''; ?>" value="<?= isset($settings_data['host_name']) ? esc_attr($settings_data['host_name']) : ''; ?>"></td>
                </tr>
                <tr>
                    <th><label for="username"><?php _e('Nom de l\'utilisateur :', 'shamma'); ?></label></th>
                    <td><input type="text" id="username" name="username" class="<?= isset($errors['username']) ? 'error' : ''; ?>" value="<?= isset($settings_data['username']) ? esc_attr($settings_data['username']) : ''; ?>"></td>
                </tr>
                <tr>
                    <th><label for="password"><?php _e('Mot de passe :', 'shamma'); ?></label></th>
                    <td><input type="password" id="password" name="host_password" class="<?= isset($errors['host_password']) ? 'error' : ''; ?>" value="<?= isset($settings_data['host_password']) ? esc_attr($settings_data['host_password']) : ''; ?>"></td>
                </tr>
            </table>
            <?php submit_button(__('Enregistrer les paramètres', 'shamma'), 'primary', 'submit-form-settings'); ?>
        </form>
    </div>
    <?php
}

//check settings fields
function get_settings_fields()
{
    $errors = [];
    $settings_data = [];
    $error_message = __('Veuillez remplir ce champ', 'shamma');

    if (isset($_POST['submit-form-settings'])) {

        //fetch spouse fields
        $fields = [
            "host_name" => [
                "required" => true,
                "default" => "",
            ],
            "username" => [
                "required" => true,
                "default" => "",
            ],
            "host_password" => [
                "required" => true,
                "default" => "",
            ]
        ];
        //validation fields
        foreach ($fields as $key => $value) {
            if ($value["required"] && (!isset($_POST[$key]) || !$_POST[$key])) {
                if ($value["default"]) {
                    $settings_data[$key] = $value["default"];
                } else {
                    $errors[$key] = $error_message;
                }
            } else if ($value["required"] && isset($_POST[$key]) && $_POST[$key]) {
                $settings_data[$key] = sanitize_text_field($_POST[$key]);
            } else if (!$value["required"] && isset($_POST[$key]) && $_POST[$key]) {
                $settings_data[$key] = sanitize_text_field($_POST[$key]);
            } else if (!$value["required"] && $value["default"]) {
                $settings_data[$key] = $value["default"];
            }
        }
    }

    return ['settings_data' => $settings_data, 'errors' => $errors];
}
//insert settings datas in database
function insert_settings_data($settings_data)
{
    global $wpdb;
    $table_name2 = $wpdb->prefix . 'dynamic_form_hosting_settings';

    // Encodage du mot de passe en base64
    $encoded_password = base64_encode($settings_data['host_password']);

    try {
        $wpdb->query("DELETE FROM $table_name2");

        $result = $wpdb->insert(
            $table_name2,
            [
                'host_name' => $settings_data['host_name'],
                'username' => $settings_data['username'],
                'host_password' => $encoded_password,
            ],
            [
                '%s', // host_name
                '%s', // username
                '%s', // host_password
            ]
        );

        if (!$result) {
            throw new Exception(__('Erreur lors de l\'insertion des paramètres de configuration.', 'shamma'));
        } else {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Paramètres SMTP enregistrés avec succès.', 'shamma') . '</p></div>';
            });
        }
    } catch (Exception $e) {
        add_action('admin_notices', function () use ($e) {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Une erreur s\'est produite lors de l\'insertion des paramètres de configuration : ', 'shamma') . $e->getMessage() . '</p></div>';
        });
    }
}

// call function to insert settings values when they values was passed
if (isset($_POST['submit-form-settings'])) {

    $settings_form_data = get_settings_fields();
    $settings_data = $settings_form_data['settings_data'];
    $settings_errors = $settings_form_data['errors'];

    if (empty($settings_errors)) {
        insert_settings_data($settings_data);
    }
}

//generate pdf using Dom pdf
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use WPMailSMTP\Helpers\Crypto;

function generatePdf($htmlContent, $outputFilename)
{
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_path = get_attached_file($custom_logo_id);


    $nom = isset($_POST['nom']) ? sanitize_text_field($_POST['nom']) : '';
    $age = isset($_POST['age']) ? sanitize_text_field($_POST['age']) : '';
    $school_level = isset($_POST['school_level']) ? sanitize_text_field($_POST['school_level']) : '';
    $notes = isset($_POST['notes']) ? sanitize_text_field($_POST['notes']) : '';
    $experience_years = isset($_POST['experience_years']) ? sanitize_text_field($_POST['experience_years']) : '';
    $work_domain = isset($_POST['work_domain']) ? sanitize_text_field($_POST['work_domain']) : '';
    $children = isset($_POST['children']) ? sanitize_text_field($_POST['children']) : '';
    $children_details = isset($_POST['children_details']) ? sanitize_text_field($_POST['children_details']) : '';
    $family_in_canada = isset($_POST['family_in_canada']) ? sanitize_text_field($_POST['family_in_canada']) : '';
    $marital_status = isset($_POST['marital_status']) ? sanitize_text_field($_POST['marital_status']) : '';
    $spouse_name = isset($_POST['spouse_name']) ? sanitize_text_field($_POST['spouse_name']) : '';
    $spouse_age = isset($_POST['spouse_age']) ? sanitize_text_field($_POST['spouse_age']) : '';
    $spouse_school_level = isset($_POST['spouse_school_level']) ? sanitize_text_field($_POST['spouse_school_level']) : '';
    $spouse_notes = isset($_POST['spouse_notes']) ? sanitize_text_field($_POST['spouse_notes']) : '';
    $spouse_experience_years = isset($_POST['spouse_experience_years']) ? sanitize_text_field($_POST['spouse_experience_years']) : '';
    $spouse_work_domain = isset($_POST['spouse_work_domain']) ? sanitize_text_field($_POST['spouse_work_domain']) : '';
    $spouse_family_in_canada = isset($_POST['spouse_family_in_canada']) ? sanitize_text_field($_POST['spouse_family_in_canada']) : '';
    $html = '<img src="file://' . $logo_path . '" alt="Logo shamma immigration">';
    $html .= '<h1>' . __('Formulaire d\'immigration', 'shamma') . '</h1>';
    $html .= __('Félicitation Mr./(Mrs). ', 'shamma') . '<b>' . $nom . '</b>' . __(' pour avoir rempli notre formulaire !!!, vos informations sont :', 'shamma') . '<br/>';
    $html .= '<p>' . __('Âge : ', 'shamma') . $age . ' ' . __('ans', 'shamma') . '</p>';
    $html .= '<p>' . __('Niveau scolaire : ', 'shamma') . $school_level . '</p>';
    $html .= '<p>' . __('Avez-vous vos relevés de notes ? : ', 'shamma') . $notes . '</p>';
    $html .= '<p>' . __('Votre expérience professionnelle : ', 'shamma') . $experience_years . ' ' . __('ans', 'shamma') . '</p>';
    $html .= '<p>' . __('Votre domaine de travail : ', 'shamma') . $work_domain . '</p>';
    $html .= '<p>' . __('Avez-vous des enfants ? : ', 'shamma') . $children . '</p>';
    if ($children_details) {
        $html .= '<p>' . __('Si oui, combien d\'enfants et leurs tranches d\'âge : ', 'shamma') . $children_details . '</p>';
    }
    $html .= '<p>' . __('Avez-vous un frère ou une sœur au Canada ? : ', 'shamma') . $family_in_canada . '</p>';
    $html .= '<p>' . __('Êtes-vous marié(e) ? : ', 'shamma') . $marital_status . '</p>';
    if ($marital_status === 'oui') {
        $html .= '<em><p><b>' . __('---Informations relatives au conjoint---', 'shamma') . '</b></p></em>';
        $html .= '<p>' . __('Nom du conjoint : ', 'shamma') . $spouse_name . '</p>';
        $html .= '<p>' . __('Âge du conjoint: ', 'shamma') . $age . ' ' . __('ans', 'shamma') . '</p>';
        $html .= '<p>' . __('Niveau scolaire du conjoint : ', 'shamma') . $spouse_school_level . '</p>';
        $html .= '<p>' . __('Notes du conjoint : ', 'shamma') . $spouse_notes . '</p>';
        $html .= '<p>' . __('Expérience professionnelle du conjoint : ', 'shamma') . $spouse_experience_years . ' ' . __('ans', 'shamma') . '</p>';
        $html .= '<p>' . __('Domaine de travail du conjoint : ', 'shamma') . $spouse_work_domain . '</p>';
        $html .= '<p>' . __('Famille au Canada : ', 'shamma') . $spouse_family_in_canada . '</p>';
    }
    $htmlContent = $html;

    $options = new Options;
    $options->setChroot($logo_path);
    $dompdf = new Dompdf($options);
    $dompdf->setPaper('A4', 'portrait');

    $dompdf->loadHtml($htmlContent);
    $dompdf->render();
    $dompdf->addInfo('Title', __('Formulaire d\'immigration', 'shamma'));
    $output = $dompdf->output();
    file_put_contents($outputFilename, $output);
}

//send mail
function sendEmailWithAttachment($recipientEmail, $senderEmail, $attachmentPath)
{
    require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
    require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
    require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
    require_once ABSPATH . 'wp-content/plugins/wp-mail-smtp/src/Helpers/Crypto.php';


    // Créer une instance de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();


    global $wpdb;
    $table_name2 = $wpdb->prefix . 'dynamic_form_hosting_settings';

    // Requête SQL pour récupérer toutes les données de la table
    $query = "SELECT * FROM $table_name2";

    // Exécution de la requête et récupération des résultats
    $results = $wpdb->get_results($query, ARRAY_A);
    foreach ($results as $row) {
        $host = $row['host_name'];
        $username = $row['username'];
        $password = $row['host_password'];
    }
    $mdp_decode = base64_decode($password);



    $mail->isSMTP();
    $mail->Host = $host;
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $mdp_decode;


    $mail->SMTPDebug = 0;

    // Paramètres de l'email
    $mail->setFrom($senderEmail);
    $mail->addAddress($recipientEmail);
    $mail->Subject = __('Formulaire d\'immigration', 'shamma');
    $mail->Body    = __('Bonjour ', 'shamma') . $_POST['nom'] . __(', merci de nous faire confiance !!', 'shamma');

    // Ajout de la pièce jointe
    $file = $attachmentPath;
    $filename = basename($file);
    $mail->addAttachment($file, $filename);

    // Envoyer l'email
    if ($mail->send()) {
    ?>
        <div class="email-success">
            <p><?= __("Félicitation, votre formulaire a été soumis avec succès. Vous recevrez un mail recensant vos informations !", "shamma") ?></p>
        </div>

<?php
        //nettoyage du formulaire une fois qu'il a été soumis
        $form_processed_successfully = true; // Remplacez cela par votre propre logique de succès

        if ($form_processed_successfully) {
            // Redirection avec JavaScript
            echo '<script>window.location.href = "' . esc_url(add_query_arg('form_success', 'true', $_SERVER['REQUEST_URI'])) . '";</script>';
            exit;
        }
    } else {
        // echo 'Erreur lors de l\'envoi de l\'email: ' . $mail->ErrorInfo;
    }
}

$form_data_user = get_submit_form_data();
$user_data = $form_data_user['user_data'];
$user_errors = $form_data_user['errors'];

$form_data_spouse = get_submit_spouse_form_data();
$spouse_data = $form_data_spouse['other_data'];
$spouse_errors = $form_data_spouse['errors'];

// Vérifier s'il n'y a pas d'erreurs dans les données de l'utilisateur ou du conjoint
if (empty($user_errors) && empty($spouse_errors) || $spouse_id === null) {
    $outputFilename = __("Formulaire_immigration.pdf", "shamma");
    generatePdf("", $outputFilename);

    $recipientEmail = isset($_POST['mail']) ? sanitize_text_field($_POST['mail']) : '';
    $senderEmail = get_option('admin_email');
    sendEmailWithAttachment($recipientEmail, $senderEmail, $outputFilename);
}


//Insert datas in the table body of the plugin
function display_registered_users_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'dynamic_form_table';

    // Pagination parameters
    $current_page = max(1, intval($_GET['paged']));

    $records_per_page = 10;
    // Offset calculation
    $offset = ($current_page - 1) * $records_per_page;

    // Fetch records for the current page
    $users = $wpdb->get_results("SELECT * FROM $table_name LIMIT $records_per_page OFFSET $offset");

    echo '<div class="dynamic-form-table">';
    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">' . esc_html__('Liste des clients inscrits', 'shamma') . '</h1>';

    if ($users) {
        echo '<table class="widefat striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html__('ID', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Nom', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Age', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Niveau scolaire', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Relevé de notes', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Expérience professionnelle', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Domaine de travail', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Enfants', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Nbre d\'enfants et âge', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Frères/Soeurs au canada', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Statut matrimoniale', 'shamma') . '</th>';
        echo '</tr>';
        echo '</thead>';

        foreach ($users as $user) {
            echo '<tbody>';
            echo '<tr>';
            echo '<td>' . esc_html($user->id) . '</td>';
            echo '<td>' . esc_html($user->nom) . '</td>';
            echo '<td>' . esc_html($user->age) . '</td>';
            echo '<td>' . esc_html($user->school_level) . '</td>';
            echo '<td>' . esc_html($user->notes) . '</td>';
            echo '<td>' . esc_html($user->experience_years) . '</td>';
            echo '<td>' . esc_html($user->work_domain) . '</td>';
            echo '<td>' . esc_html($user->children) . '</td>';
            echo '<td>' . esc_html($user->children_details) . '</td>';
            echo '<td>' . esc_html($user->family_in_canada) . '</td>';
            echo '<td>' . esc_html($user->marital_status) . '</td>';
            echo '</tr>';
            echo '</tbody>';
        }

        echo '<tfoot>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html__('ID', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Nom', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Age', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Niveau scolaire', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Relevé de notes', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Expérience professionnelle', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Domaine de travail', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Enfants', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Nbre d\'enfants et âge', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Frères/Soeurs au canada', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Statut matrimoniale', 'shamma') . '</th>';
        echo '</tr>';
        echo '</tfoot>';
        echo '</table>';


        $total_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $total_pages = ceil($total_records / $records_per_page);

        if ($total_records >= 11) {
            echo '<div class="tablenav">';
            echo '<div class="tablenav-pages">';
            echo paginate_links(array(
                'base' => esc_url(add_query_arg('paged', '%#%')),
                'format' => '?paged=%#%',
                'prev_text' => __('&laquo; Précédent', 'shamma'),
                'next_text' => __('Suivant &raquo;', 'shamma'),
                'total' => $total_pages,
                'current' => $current_page,
            ));

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>' . esc_html__('No records found.', 'shamma') . '</p>';
    }

    echo '</div>';
    echo '</div>';
}

add_action('admin_menu', 'add_registered_users_submenu');

//insert conjoint list on the body of the plugin
function display_registered_spouses_table()
{
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'dynamic_form_spouse_table';

    // Pagination parameters
    $current_page = max(1, intval($_GET['paged']));
    $records_per_page = 10;
    $offset = ($current_page - 1) * $records_per_page;

    // Fetch records for the current page with spouse name
    $spouses = $wpdb->get_results("
        SELECT s.*, t.nom AS mspouse_name, t.marital_status AS married
        FROM $table_name1 s
        LEFT JOIN {$wpdb->prefix}dynamic_form_table t ON s.spouse_id = t.id
        WHERE t.marital_status = 'oui'
        LIMIT $records_per_page OFFSET $offset
    ");

    echo '<div class="dynamic-form-table">';
    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">' . esc_html__('Liste des conjoints inscrits', 'shamma') . '</h1>';

    if ($spouses) {
        echo '<table class="widefat striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html__('ID', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Nom', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Epoux(se) de', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Age', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Niveau scolaire', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Relevé de notes', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Expérience professionnelle', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Domaine de travail', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Frères/Soeurs au canada', 'shamma') . '</th>';
        echo '</tr>';
        echo '</thead>';

        foreach ($spouses as $spouse) {
            echo '<tbody>';
            echo '<tr>';
            echo '<td>' . esc_html($spouse->spouse_id) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_name) . '</td>';
            echo '<td>' . esc_html($spouse->mspouse_name) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_age) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_school_level) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_notes) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_experience_years) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_work_domain) . '</td>';
            echo '<td>' . esc_html($spouse->spouse_family_in_canada) . '</td>';
            echo '</tr>';
            echo '</tbody>';
        }

        echo '<tfoot>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html__('ID', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Nom', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Epoux(se) de', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Age', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Niveau scolaire', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Relevé de notes', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Expérience professionnelle', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Domaine de travail', 'shamma') . '</th>';
        echo '<th scope="col">' . esc_html__('Frères/Soeurs au canada', 'shamma') . '</th>';
        echo '</tr>';
        echo '</tfoot>';
        echo '</table>';


        $total_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name1");
        $total_pages = ceil($total_records / $records_per_page);

        if ($total_records >= 11) {
            echo '<div class="tablenav">';
            echo '<div class="tablenav-pages">';
            echo paginate_links(array(
                'base' => esc_url(add_query_arg('paged', '%#%')),
                'format' => '?paged=%#%',
                'prev_text' => __('&laquo; Précédent', 'shamma'),
                'next_text' => __('Suivant &raquo;', 'shamma'),
                'total' => $total_pages,
                'current' => $current_page,
            ));

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>' . esc_html__('Aucun enregistrement trouvé.', 'shamma') . '</p>';
    }



    echo '</div>';
    echo '</div>';
}

function add_registered_users_submenu()
{
    // Ajouter le menu principal (Dynamic Form)
    add_menu_page(
        __('Dynamic Form', 'shamma'), // Titre de la page
        __('Dynamic Form', 'shamma'), // Texte du menu
        'manage_options', // Capacité nécessaire pour accéder à la page
        'dynamic_form_menu', // Slug de la page
        'display_registered_users_table', // Fonction d'affichage de la page
        'dashicons-email-alt2', // Icône du menu
        3 // Position dans le menu (ajustez selon vos besoins)
    );
    // Ajouter les sous-menus
    add_submenu_page(
        'dynamic_form_menu', // Slug du menu principal
        __('Liste des Clients', 'shamma'), // Titre de la page
        __('Liste des Clients', 'shamma'), // Texte du menu
        'manage_options',
        'registered_users',
        'display_registered_users_table'
    );
    add_submenu_page(
        'dynamic_form_menu', // Slug du menu principal
        __('Liste des conjoints', 'shamma'), // Titre de la page
        __('Liste des conjoints', 'shamma'), // Texte du menu
        'manage_options',
        'registered_spouses',
        'display_registered_spouses_table'
    );

    add_submenu_page(
        'dynamic_form_menu',
        __('Paramètres', 'shamma'),
        __('Paramètres', 'shamma'),
        'manage_options',
        'dynamic_form_settings',
        'display_dynamic_form_settings'
    );
}
// Ajouter l'action pour créer les menus
add_action('admin_menu', 'add_registered_users_submenu');

function remove_dynamic_form_submenu()
{
    remove_submenu_page('dynamic_form_menu', 'dynamic_form_menu');
}

// Ajouter l'action pour supprimer le sous-menu redondant
add_action('admin_menu', 'remove_dynamic_form_submenu');

// Function who drop the table to database when i uninstall the plugin
function dynamic_form_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'dynamic_form_table';
    $table_name1 = $wpdb->prefix . 'dynamic_form_spouse_table';
    $table_name2 = $wpdb->prefix . 'dynamic_form_hosting_settings';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name1'") == $table_name1) {
        $wpdb->query("DROP TABLE IF EXISTS $table_name1");
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name2'") == $table_name2) {
        $wpdb->query("DROP TABLE IF EXISTS $table_name2");
    }
}

// register the dynamic_form_uninstall when i uninstall my plugin
// register_uninstall_hook(__FILE__, 'dynamic_form_uninstall');
register_deactivation_hook(__FILE__, 'dynamic_form_uninstall');

//register scripts and styles
function add_custom_css_and_js()
{
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('custom-form', $plugin_url . 'assets/css/custom-form.css');
    wp_enqueue_script('manage-fields', $plugin_url . 'assets/js/manage-dynamic-fields.js');
}
add_action('wp_enqueue_scripts', 'add_custom_css_and_js');

// register style for a back-office
function load_custom_admin_styles()
{
    $plugin_dir = plugin_dir_url(__FILE__);
    wp_register_style('custom-admin-styles', $plugin_dir . 'assets/css/custom-admin-styles.css', array(), '1.0.0', 'all');

    wp_enqueue_style('custom-admin-styles');
}
add_action('admin_enqueue_scripts', 'load_custom_admin_styles');

//load text domain
function plugin_name_load_plugin_textdomain()
{

    $domain = 'shamma';
    $locale = apply_filters('plugin_locale', get_locale(), $domain);

    if ($loaded = load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo')) {
        return $loaded;
    } else {
        load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages/');
    }
}

add_action('init', 'plugin_name_load_plugin_textdomain');
