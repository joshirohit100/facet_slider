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

        $form[$facet->getFieldAlias() . '_slider'] = array(
            '#markup' => '<div class="facet-slider-slider"></div>',
        );

        $form['#attributes'] = [
            'class' => ['facet-slider-facet'],
        ];

        $form['#attached'] = [
            'library' => ['facet_slider/facets.facet_slider'],
            'drupalSettings' => [
                'facet_slider' => [
                 $facet->getFieldAlias() => [
                        'max' => max($max),
                        'step' => $configuration['slider_step'] ? : 1,
                        'prefix' => $configuration['slider_prefix'] ? : '',
                        'suffix' => $configuration['slider_suffix'] ? : '',
                    ],
                ],
            ],
        ];

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
        $facet = $this->facet;dpm($facet->getResults());

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

        // The form was submitted but nothing was active in the form, we should
        // still redirect, but the url for the new page can't come from a result.
        // So we're redirecting to the facet source's page.
        $path = $facet->getFacetSource()->getPath();
        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        $option = ['query' => 'f[0]='.$facet->getFieldAlias() . ':' . $active_items];
        $link = Url::fromUserInput($path);
        $link->setAbsolute();dpm($link);
        $form_state->setResponse(new RedirectResponse($link->toString()));
    }

}