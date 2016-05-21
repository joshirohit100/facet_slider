<?php

namespace Drupal\facet_slider\Plugin\facets\widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\facets\FacetInterface;
use Drupal\facet_slider\Form\SliderWidgetForm;
use Drupal\facets\Widget\WidgetInterface;

/**
 * The slider widget.
 *
 * @FacetsWidget(
 *   id = "slider",
 *   label = @Translation("Slider"),
 *   description = @Translation("A configurable widget that show as a slider"),
 * )
 */
class SliderWidget implements WidgetInterface {

    use StringTranslationTrait;

    /**
     * {@inheritdoc}
     */
    public function build(FacetInterface $facet) {
        $form_builder = \Drupal::getContainer()->get('form_builder');
        $form_object = new SliderWidgetForm($facet);
        return $form_builder->getForm($form_object);
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state, $config) {

        $form['slider_step'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Step value'),
        ];
        $form['slider_prefix'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Prefix'),
        ];
        $form['slider_suffix'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Suffix'),
        ];

        if (!is_null($config)) {
            $widget_configs = $config->get('widget_configs');
            if (isset($widget_configs['slider_step'])) {
                $form['slider_step']['#default_value'] = $widget_configs['slider_step'];
            }
            if (isset($widget_configs['slider_prefix'])) {
                $form['slider_prefix']['#default_value'] = $widget_configs['slider_prefix'];
            }
            if (isset($widget_configs['slider_suffix'])) {
                $form['slider_suffix']['#default_value'] = $widget_configs['slider_suffix'];
            }
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryType($query_types) {
        return $query_types['string'];
    }

}
