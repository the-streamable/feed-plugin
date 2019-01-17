<?php

/**
 * Feed Plugin
 *
 * @version 3.0.0
 */

Kirby::plugin('the-streamable/feed', [
    'pagesMethods' => [
        'feed' => function ($params = []) {
            // set all default values
            $defaults = [
                'url'               => url(),
                'title'             => 'Feed',
                'description'       => '',
                'link'              => url(),
                'datefield'         => 'date',
                'textfield'         => 'text',
                'modified'          => time(),
                'excerpt'           => false,
                'generator'         => kirby()->option('feed.generator', 'Kirby'),
                'header'            => true,
                'snippet'           => false,
                'creatorfield'      => false,
                'enclosurefield'    => false,
                'languagecode'      => site()->language() ? site()->language()->code() : 'en',
            ];
            
            // merge them with the user input
            $options = array_merge($defaults, $params);
            
            // sort by date
            $items = $this->sortBy($options['datefield'], 'desc');
            
            // add the items
            $options['items'] = $items;
            $options['link'] = url($options['link']);
            
            // fetch the modification date
            if ($items->count()) {
                if ($options['datefield'] == 'modified') {
                    $options['modified'] = $items->first()->modified();
                }
                else {
                    $dateFieldName = $options['datefield'];
                    $options['modified'] = $items->first()->$dateFieldName()->toDate();
                }
            }
            
            // send the xml header
            if ($options['header']) {
                header::type('text/xml');
            }
            
            // echo the doctype
            $html = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
            
            // custom snippet
            if ($options['snippet']) {
                $html .= snippet($options['snippet'], $options, true);
            }
            else {
                $html .= tpl::load(__DIR__ . '/template.php', $options);
            }
            
            return $html;
        }
    ]
]);