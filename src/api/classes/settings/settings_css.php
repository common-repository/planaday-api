<?php

class settings_css extends settings
{
	/**
	 * @return settingsWelcome
	 */
	public static function planaday_api_get_instance() {
		static $instance;

		if ( $instance === null ) {
			$instance = new static();
		}

		return $instance;
	}

    /**
     * @param $input
     * @return array
     */
    public function planaday_api_css_validate($input)
    {
        $message = null;
        $type = null;
        $valid = [];
        $valid['sccss-content'] = $input['sccss-content'];

        if (empty($type)) {
            $type = 'updated';
            $message = __('Successfully saved', $this->getClassName());
        }

        add_settings_error(
            'planaday-api-css',
            esc_attr('settings_updated'),
            $message,
            $type
        );

        return $valid;
    }

    public function planaday_api_css_page()
    {
        $options = get_option('planaday-api-css');
        $content = isset($options['sccss-content']) && !empty($options['sccss-content']) ?
            $options['sccss-content'] :
            __('/* Voeg hier jouw eigen CSS toe */', 'planaday-api-css');

        echo '<div class="wrap">';
        echo '<h2>' . esc_html(get_admin_page_title()) . ' CSS</h2>';
        echo '<form method="post" name="planaday_options" action="options.php">';

        settings_errors();
        settings_fields('planaday-api-css');
        do_settings_sections('planaday-api');
        ?>

        <table class="form-table">
            <tr>
                <td colspan="2"><h3 style="color: #ca4a1f;">Jouw kleuren en opmaak</h3>
                    <p>
                        Met CSS kun je de opmaak en de kleuren van alle teksten aanpassen.<br />
                        Vul hieronder de CSS in zodat de teksten, kleuren en opmaak gewijzigd kan worden.<br />
                        Pas bijvoorbeeld de titel van de cursus aan in oranje:
                        <pre style="background-color: #fff4f4; width: 90%; border: 1px solid #dddddd; padding: 5px;">.pad-title, .cursustitel {
  color: #C1121C !important;
}</pre>
                    <br/>Pas onderdelen zoals kosten, dagdelen etc aan:
                        <pre style="background-color: #fff4f4; width: 90%; border: 1px solid #dddddd; padding: 5px;">.pad-dayparts, .pad-place, .pad-available, .pad-detail-available,
.pad-detail-costs, .pad-detail-amount, .pad-detail-amount, .pad-detail-costs, .pad-detail-available, .pad-detail-garanteed,
.pad-detail-moneygaranteed, .pad-costs, .pad-courseelearning {
  color: #C1121C !important;
}</pre>
                    <br/>De button aanpassen:
                        <pre style="background-color: #fff4f4; width: 90%; border: 1px solid #dddddd; padding: 5px;">.btn-link {
  background-color: #C1121C !important;
  color: black !important;
}</pre>
                    <br/>De velden onder elkaar plaatsen bij bedrijf:
                        <pre style="background-color: #fff4f4; width: 90%; border: 1px solid #dddddd; padding: 5px;">#company_details label {
  display: contents !important;
}</pre>
                    <br/>Afbeelding in overzichten 250px breed maken en afbeelding links naast tekst:
                        <pre style="background-color: #fff4f4; width: 90%; border: 1px solid #dddddd; padding: 5px;">.pad-imagecover-overzicht {
  max-width: 250px !important;
  padding: 5px;
  float: left;
}</pre>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3 style="color: #ca4a1f;">Geef hier jouw CSS op</h3>
                    <textarea cols="50" rows="30" name="planaday-api-css[sccss-content]" class="sccss-content"
                              id="sccss_settings[sccss-content]"><?php echo esc_html($content); ?></textarea>
                </td>
            </tr>

        </table>

        <?php
        echo submit_button(__('Alle Instellingen opslaan', 'planaday-api-css'), 'primary', 'submit', true);

        echo '</form>';
        echo '</div>';
    }
}
