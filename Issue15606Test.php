<?php
namespace AppBundle\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormBuilder;

class Issue15606Test extends WebTestCase
{
    private $arrayWithDuplicateContent = [
         2 => "Category A",
         3 => " ...  Category A.1",
         4 => " ...  Category A.2",
         5 => "Category B",
         6 => " ...  Category B.1",
         7 => " ...  ... inactive",
         8 => " ...  ... active",
         9 => " ...  Category B.2",
        10 => " ...  ... inactive",
        11 => " ...  ... active",
        12 => " ... Category B.3",
        13 => " ...  ... Category B.3.1",
        14 => " ...  ... Category B.3.2",
        15 => " ... Category B.4",
        16 => " ...  ... Category B.4.1",
        17 => " ...  ... Category B.4.2",
        18 => " ... Category B.5",
        19 => " ...  ... Category B.5.1",
        20 => " ...  ... Category B.5.2",
    ];

    private function createFormView($options)
    {
        $client    = static::createClient();
        $container = $client->getContainer();

        /** @var FormBuilder $formBuilder */
        $formBuilder = $container->get('form.factory')->createBuilder('form');
        $form        = $formBuilder->add('category', 'choice', $options)->getForm();

        return $form->createView();
    }

    public function testBigArrayChoicesAsValues()
    {
        $formView = $this->createFormView(['choices' => $this->arrayWithDuplicateContent, 'choices_as_values' => true]);

        $convertedChoices = $formView->offsetGet('category')->vars['choices'];
        $this->assertCount(count($this->arrayWithDuplicateContent), $convertedChoices, 'Choices missing!');

        $counter = 0;
        foreach (array_keys($this->arrayWithDuplicateContent) as $key) {
            /** @var ChoiceView $choice */
            $choice = $convertedChoices[$counter];
            $this->assertEquals($key, $choice->label);
            $counter++;
        }
    }

    public function testBigArrayChoices()
    {
        $formView = $this->createFormView(['choices' => $this->arrayWithDuplicateContent, 'choices_as_values' => false]);

        $convertedChoices = $formView->offsetGet('category')->vars['choices'];
        var_dump($convertedChoices);
        $this->assertCount(count($this->arrayWithDuplicateContent), $convertedChoices, 'Choices missing!');

        $counter = 0;
        foreach ($this->arrayWithDuplicateContent as $key => $value) {
            /** @var ChoiceView $choice */
            $choice = $convertedChoices[$counter];
            $this->assertEquals($key, $choice->data);
            $this->assertEquals((string) $key, $choice->value);
            $this->assertEquals($value, $choice->label);
            $counter++;
        }
    }
}
