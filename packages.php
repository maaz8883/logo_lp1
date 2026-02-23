<?php

/**
 * Function to get package details and features based on the amount.
 * Centralizes package configuration for the brand portal.
 * 
 * @param mixed $amount
 * @return array
 */
function getPackageFeatures($amount)
{
    // Clean amount input
    $amount = (float) str_replace(',', '', $amount);

    $packages = [
        '19' => [
            'name' => 'BASIC LOGO',
            'features' => [
                '4 Logo Concepts',
                '5 Revisions',
                '1 Dedicated Designer',
                'Free Color Options',
                'Free Grayscale Format',
                '24-48 Hours TAT',
                'File Formats (JPG)',
                '100% Ownership Rights',
                '100% Money-back Guarantee',
                '24/7 Expert Support'
            ]
        ],
        '75' => [
            'name' => 'STANDARD LOGO',
            'features' => [
                '5 Logo Design Concepts',
                '8 Revisions',
                '2 Dedicated Designers',
                'Free Stationery Design (Business Card)',
                'Free Color Options',
                'Free Grayscale Format',
                'Free Icon',
                '24-48 Hours TAT',
                'File Formats: PNG, JPG',
                '100% Ownership Rights',
                '100% Money-Back Guarantee',
                '24/7 Expert Support'
            ]
        ],
        '125' => [
            'name' => 'ADVANCE LOGO',
            'features' => [
                '6 Logo Design Concepts',
                '10 Revisions',
                '3 Dedicated Designers',
                'Free Stationery (Business Card, Letterhead)',
                'Free Icon',
                'Free Email Signature',
                'Social Media Designs (Any 3 Platforms)',
                'Free Color Options & Grayscale',
                '24-48 Hours TAT',
                'File Formats: AI, PSD, EPS, PNG, JPG, PDF',
                '100% Ownership Rights',
                '100% Money-Back Guarantee',
                '24/7 Expert Support'
            ]
        ],
        '175' => [
            'name' => 'PREMIUM LOGO',
            'features' => [
                '8 Custom Logo Design Concepts',
                '6 Award-Winning Designers',
                'Unlimited Revisions',
                'Social Media Banner',
                'Free Icon Design',
                'Free Custom Stationery (Letterhead, Card, Invoice)',
                'Flyer OR Bi-Fold Brochure',
                'Free MS Electronic Letterhead',
                'Email Signature Design',
                '48-72 Hours TAT',
                'File Formats: AI, PSD, EPS, PNG, JPG, PDF',
                '100% Satisfaction Guarantee'
            ]
        ],
        '249' => [
            'name' => 'ENTERPRISE LOGO',
            'features' => [
                'Unlimited Logo Concepts',
                '6 Award-Winning Designers',
                'Unlimited Revisions',
                'Social Media Banner',
                'Free Icon Design',
                'Free Custom Stationery (Letterhead, Card, Invoice)',
                'Bi-Fold or Tri-Fold Brochure',
                'Free MS Electronic Letterhead',
                'Email Signature Design',
                '48-72 Hours TAT',
                'File Formats: AI, PSD, EPS, PNG, JPG, PDF',
                '100% Satisfaction Guarantee'
            ]
        ],
        '229' => [
            'name' => 'CORPORATE LOGO',
            'features' => [
                'Unlimited Custom Logo Concepts',
                '8 Award-Winning Designers',
                'Unlimited Revisions',
                '1 Website Prototype',
                'Free Icon Design',
                'Free Custom Stationery (Letterhead, Card, Invoice)',
                'Z-Fold Flyer',
                'Free MS Electronic Letterhead',
                'Email Signature Design',
                '48-72 Hours TAT',
                'File Formats: AI, PSD, EPS, PNG, JPG, PDF',
                '100% Satisfaction Guarantee'
            ]
        ],
        '149' => [
            'name' => 'BEGINNER WEBSITE',
            'features' => [
                '1 Page Website Design',
                'Unlimited Revisions',
                '3 Stock Photos',
                '1 Banner Design',
                'Contact/Query Form',
                'Complete W3C Certified HTML',
                'Complete Deployment',
                'Dedicated Project Manager',
                '100% Ownership Rights',
                '100% Satisfaction Guarantee'
            ]
        ],
        '299' => [
            'name' => 'BUDGET WEBSITE',
            'features' => [
                '3-Page Custom WordPress Website',
                '1 Design Concept',
                '3 Stock Images',
                'Content Integration',
                'Cross-Browser Compatible',
                'Secure Admin Tools',
                'Website Optimization',
                'Contact Form Integration',
                'Dedicated Account Manager',
                'Mobile Responsive Website',
                'CMS Backend Included',
                'SEO Friendly Website'
            ]
        ],
        '1599' => [
            'name' => 'ENTERPRISE PLUS WEBSITE',
            'features' => [
                'Unlimited Custom WordPress Website',
                '2 Design Concepts',
                '12 Stock Images',
                'WordPress CMS Backend',
                'SEO Friendly Website',
                'Google Analytics Integration',
                'Email Automation Integration',
                'Free CMS Integration',
                'Content Integration',
                'Mobile Responsive Website',
                'Cross-Browser Compatible',
                'Secure Admin Tools',
                'Website Optimization',
                'Contact Form Integration',
                'Dedicated Account Manager'
            ]
        ]
    ];

    $amtStr = (string) $amount;
    if (isset($packages[$amtStr])) {
        return $packages[$amtStr];
    }

    // Default fallback
    return [
        'name' => 'CUSTOM PACKAGE',
        'features' => [
            'Custom Logo/Web Solutions',
            'Full Ownership Rights',
            '24/7 Support',
            'Money-back Guarantee'
        ]
    ];
}
