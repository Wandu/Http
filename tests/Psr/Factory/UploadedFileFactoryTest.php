<?php
namespace Wandu\Http\Psr\Factory;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Http\Psr\UploadedFile;

class UploadedFileFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Psr\Factory\UploadedFileFactory */
    protected $factory;

    public function setUp()
    {
        $this->factory = new UploadedFileFactory();
    }

    public function testSimple()
    {
        $this->assertEquals([], $this->factory->fromFiles([]));
        $this->assertEquals([
            'main' => new UploadedFile('/private/var/abcdefg', 6171, 0, 'img.png', 'image/png'),
            'other' => new UploadedFile('/private/var/abcdefg', 6175, 0, 'img.png', 'image/png')
        ], $this->factory->fromFiles([
            'main' => [
                'name' => 'img.png',
                'type' => 'image/png',
                'tmp_name' => '/private/var/abcdefg',
                'error' => 0,
                'size' => 6171
            ],
            'other' => [
                'name' => 'img.png',
                'type' => 'image/png',
                'tmp_name' => '/private/var/abcdefg',
                'error' => 0,
                'size' => 6175
            ]
        ]));
    }

    public function testOneDepth()
    {
        // with seq array
        $this->assertEquals([
            'main' => [
                new UploadedFile('/private/var/abcdefg', 6171, 0, 'img.png', 'image/png'),
                new UploadedFile('/private/var/abcdefg', 6171, 0, 'img.png', 'image/png')
            ]
        ], $this->factory->fromFiles([
            'main' => [
                'name' => ['img.png', 'img.png'],
                'type' => ['image/png', 'image/png'],
                'tmp_name' => ['/private/var/abcdefg', '/private/var/abcdefg'],
                'error' => [0, 0],
                'size' => [6171, 6171]
            ]
        ]));

        // with assoc array
        $this->assertEquals([
            'main' => [
                'sub1' => new UploadedFile('/private/var/abcdefg', 6171, 0, 'img.png', 'image/png'),
                'sub2' => new UploadedFile('/private/var/abcdef2', 6172, 0, 'img2.png', 'image/png')
            ]
        ], $this->factory->fromFiles([
            'main' => [
                'name' => [
                    'sub1' => 'img.png',
                    'sub2' => 'img2.png'
                ],
                'type' => [
                    'sub1' => 'image/png',
                    'sub2' => 'image/png'
                ],
                'tmp_name' => [
                    'sub1' => '/private/var/abcdefg',
                    'sub2' => '/private/var/abcdef2'
                ],
                'error' => [
                    'sub1' => 0,
                    'sub2' => 0
                ],
                'size' => [
                    'sub1' => 6171,
                    'sub2' => 6172
                ]
            ]
        ]));
    }

    public function testMultiDepth()
    {
        $this->assertEquals([
            'main' => [
                'sub1' => [
                    new UploadedFile('/private/var/abcdefg', 6171, 0, 'sub1_0.png', 'image/png'),
                    new UploadedFile('/private/var/abcdefg', 6172, 0, 'sub1_1.png', 'image/png')
                ],
                'sub2' => [
                    'sub1' => new UploadedFile('/private/var/abcdefg', 6173, 0, 'sub2_sub1.png', 'image/png'),
                    'sub2' => new UploadedFile('/private/var/abcdefg', 6174, 0, 'sub2_sub2.png', 'image/png')
                ]
            ]
        ], $this->factory->fromFiles([
            'main' => [
                'name' => [
                    'sub1' => ['sub1_0.png', 'sub1_1.png'],
                    'sub2' => [
                        'sub1' => 'sub2_sub1.png',
                        'sub2' => 'sub2_sub2.png'
                    ]
                ],
                'type' => [
                    'sub1' => ['image/png', 'image/png'],
                    'sub2' => [
                        'sub1' => 'image/png',
                        'sub2' => 'image/png'
                    ]
                ],
                'tmp_name' => [
                    'sub1' => ['/private/var/abcdefg', '/private/var/abcdefg'],
                    'sub2' => [
                        'sub1' => '/private/var/abcdefg',
                        'sub2' => '/private/var/abcdefg',
                    ]
                ],
                'error' => [
                    'sub1' => [0, 0],
                    'sub2' => [
                        'sub1' => 0,
                        'sub2' => 0
                    ]
                ],
                'size' => [
                    'sub1' => [6171, 6172],
                    'sub2' => [
                        'sub1' => 6173,
                        'sub2' => 6174
                    ]
                ]
            ]
        ]));
    }
}
