<?php

namespace Drupal\facet_slider\Form;

use Drupal\Core\Form\BaseFormIdInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\facets\FacetInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SliderWidgetForm implements BaseFormIdInterface {
    /**
     * The facet to build the slider.
     *
     * @var FacetInterface $facet
     */
    protected $facet;

    /**
     * Class constructor.
     *
     * @param \Drupal\facets\FacetInterface $facet
     *   The facet to build the form for.
     */
    public function __construct(FacetInterface $facet) {
        $this->facet = $facet;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseFormId() {
        return 'facets_slider_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return $this->getBaseFormId() . '__' . $this->facet->id();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $facet = $this->facet;

        /** @var \Drupal\facets\Result\Result[] $results */
        $results = $facet->getResults();

        $max = [];
        foreach ($results as $result) {
            $max[] = $result->getRawValue();
        }
        $configuration = $facet->getWidgetConfigs();
        $default_val = $results[0]->isActive() ? $results[0]->getRawValue() : 0;

        $form[$facet->getFieldAlias()] = [
            '#type' => 'range',
            '#max'  => max($max),
            '#min'  =>  0,
            '#step' => (bool) isset($configuration['slider_step']) ? $configuration['slider_step'] : 1,
            '#suffix' => '<div class="facet-slider-val">' . $default_val . '</div>',
            '#default_value' => $default_val,
        ];

        if ($configuration['slider_submit_button']) {
            $form[$facet->id() . '_submit'] = [
                '#type' => 'submit',
                '#value' => $configuration['slider_submit_button_txt'],
            ];
        }

        $form['#attributes'] = [
            'class' => ['facet-slider-facet'],
        ];
        $form['#attached']['library'][] = 'facet_slider/facets.facet_slider';

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {}

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        $facet = $this->facet;

        $result_link = FALSE;
        $active_items = [$values[$facet->getFieldAlias()]];

        foreach ($facet->getResults() as $result) {
            if (in_array($result->getRawValue(), $active_items)) {
                $result_link = $result->getUrl();
            }
        }

        if ($result_link instanceof Url) {
            $result_link->setAbsolute();
            $form_state->setResponse(new RedirectResponse($result_link->toString()));
            return;
        }
    }

}