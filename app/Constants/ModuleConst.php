<?php

namespace App\Constants;

/**
 * Constants for module names in the system.
 */
final class ModuleConst
{
    // Module names
    public const MODULE_ROLE_MANAGEMENT = 'role_management';
    public const MODULE_USER_MANAGEMENT = 'user_management';
    public const MODULE_SECTION_MANAGEMENT = 'section_management';
    public const MODULE_PRODUCT_MANAGEMENT = 'product_management';
    public const MODULE_ENTERPRISE_MANAGEMENT = 'enterprise_management';
    public const MODULE_POST_MANAGEMENT = 'post_management';
    public const MODULE_REDIRECT_POST_MANAGEMENT = 'redirect_post_management';
    public const MODULE_REVIEW_POST_MANAGEMENT = 'review_post_management';
    public const MODULE_SCHOOL_POST_MANAGEMENT = 'school_post_management';
    public const MODULE_QUOTE_POST_MANAGEMENT = 'quote_post_management';
    public const MODULE_CATEGORY_MANAGEMENT = 'category_management';
    public const MODULE_EVENT_MANAGEMENT = 'event_management';
    public const MODULE_AWARD_MANAGEMENT = 'award_management';
    public const MODULE_STANDARD_MANAGEMENT = 'standard_management';
    public const MODULE_URL_SHORTENER_MANAGEMENT = 'url_shortener_management';
    public const MODULE_PREGNANCY_WEEK_MANAGEMENT = 'pregnancy_week_management';

    /**
     * Module configuration with categories, labels, icons, and sub-modules.
     */
    private static array $moduleConfig = [
        'system_management' => [
            'label' => 'system_management',
            'modules' => [
                self::MODULE_ROLE_MANAGEMENT => [
                    'label' => 'role_management',
                    'icon' => '<i class="fa-light fa-user-shield me-2"></i>',
                    'children' => [
                        ['name' => 'role.index', 'label' => 'role_management.index'],
                        ['name' => 'role.create', 'label' => 'role_management.create'],
                    ],
                ],
                self::MODULE_USER_MANAGEMENT => [
                    'label' => 'user_management',
                    'icon' => '<i class="fa-light fa-users me-2"></i>',
                    'children' => [
                        ['name' => 'user.index', 'label' => 'user_management.index'],
                        ['name' => 'user.create', 'label' => 'user_management.create'],
                    ],
                ],
                self::MODULE_SECTION_MANAGEMENT => [
                    'label' => 'section_management',
                    'icon' => '<i class="fa-light fa-cube me-2"></i>',
                    'children' => [
                        ['name' => 'section.index', 'label' => 'section_management.index'],
                        ['name' => 'section.create', 'label' => 'section_management.create'],
                    ],
                ],
            ],
        ],
        'pregnancy_week_management' => [
            'label' => 'pregnancy_week_management',
            'modules' => [
                self::MODULE_PREGNANCY_WEEK_MANAGEMENT => [
                    'label' => 'pregnancy_week_management',
                    'icon' => '<i class="fa-light fa-person-pregnant me-2"></i>',
                    'children' => [
                        ['name' => 'pregnancy-week.index', 'label' => 'pregnancy_week_management.index'],
                    ],
                ],
            ],
        ],
        'content_management' => [
            'label' => 'content_management',
            'modules' => [
                self::MODULE_POST_MANAGEMENT => [
                    'label' => 'post_management',
                    'icon' => '<i class="fa-light fa-blog me-2"></i>',
                    'children' => [
                        ['name' => 'post.index', 'label' => 'post_management.index'],
                        ['name' => 'post.create', 'label' => 'post_management.create'],
                    ],
                ],
                self::MODULE_REDIRECT_POST_MANAGEMENT => [
                    'label' => 'redirect_post_management',
                    'icon' => '<i class="fa-light fa-blog me-2"></i>',
                    'children' => [
                        ['name' => 'redirect-posts.index', 'label' => 'redirect_post_management.index'],
                        ['name' => 'redirect-posts.create', 'label' => 'redirect_post_management.create'],
                    ],
                ],
                self::MODULE_REVIEW_POST_MANAGEMENT => [
                    'label' => 'review_post_management',
                    'icon' => '<i class="fa-light fa-blog me-2"></i>',
                    'children' => [
                        ['name' => 'review-posts.index', 'label' => 'review_post_management.index'],
                        ['name' => 'review-posts.create', 'label' => 'review_post_management.create'],
                    ],
                ],
                self::MODULE_SCHOOL_POST_MANAGEMENT => [
                    'label' => 'school_post_management',
                    'icon' => '<i class="fa-light fa-blog me-2"></i>',
                    'children' => [
                        ['name' => 'school-posts.index', 'label' => 'school_post_management.index'],
                        ['name' => 'school-posts.create', 'label' => 'school_post_management.create'],
                    ],
                ],
                self::MODULE_QUOTE_POST_MANAGEMENT => [
                    'label' => 'quote_post_management',
                    'icon' => '<i class="fa-light fa-blog me-2"></i>',
                    'children' => [
                        ['name' => 'quote-posts.index', 'label' => 'quote_post_management.index'],
                        ['name' => 'quote-posts.create', 'label' => 'quote_post_management.create'],
                    ],
                ],
                self::MODULE_EVENT_MANAGEMENT => [
                    'label' => 'event_management',
                    'icon' => '<i class="fa-light fa-calendar me-2"></i>',
                    'children' => [
                        ['name' => 'event.index', 'label' => 'event_management.index'],
                        ['name' => 'event.create', 'label' => 'event_management.create'],
                    ],
                ],
                self::MODULE_AWARD_MANAGEMENT => [
                    'label' => 'award_management',
                    'icon' => '<i class="fa-light fa-calendar me-2"></i>',
                    'children' => [
                        ['name' => 'award.index', 'label' => 'award_management.index'],
                        ['name' => 'award.create', 'label' => 'award_management.create'],
                    ],
                ],
                self::MODULE_CATEGORY_MANAGEMENT => [
                    'label' => 'category_management',
                    'icon' => '<i class="fa-light fa-layer-group me-2"></i>',
                    'children' => [
                        ['name' => 'category.index', 'label' => 'category_management.index'],
                        ['name' => 'category.create', 'label' => 'category_management.create'],
                    ],
                ],
            ],
        ],
        'product_management' => [
            'label' => 'product_management',
            'modules' => [
                self::MODULE_STANDARD_MANAGEMENT => [
                    'label' => 'standard_management',
                    'icon' => '<i class="fa-light fa-standard-definition me-2"></i>',
                    'children' => [
                        ['name' => 'standard.index', 'label' => 'standard_management.index'],
                    ],
                ],
                self::MODULE_PRODUCT_MANAGEMENT => [
                    'label' => 'product_management',
                    'icon' => '<i class="fa-light fa-box me-2"></i>',
                    'children' => [
                        ['name' => 'product.index', 'label' => 'product_management.index'],
                        ['name' => 'product.create', 'label' => 'product_management.create'],
                    ],
                ],
            ],
        ],
        'enterprise_management' => [
            'label' => 'enterprise_management',
            'modules' => [
                self::MODULE_ENTERPRISE_MANAGEMENT => [
                    'label' => 'enterprise_management',
                    'icon' => '<i class="fa-light fa-building me-2"></i>',
                    'children' => [
                        ['name' => 'enterprise.index', 'label' => 'enterprise_management.index'],
                        ['name' => 'enterprise.create', 'label' => 'enterprise_management.create'],
                    ],
                ],
            ],
        ],
        'url_shortener_management' => [
            'label' => 'url_shortener_management',
            'modules' => [
                self::MODULE_URL_SHORTENER_MANAGEMENT => [
                    'label' => 'url_shortener_management',
                    'icon' => '<i class="fa-light fa-link me-2"></i>',
                    'children' => [
                        ['name' => 'url-shortener.index', 'label' => 'url_shortener_management.index'],
                        ['name' => 'url-shortener.create', 'label' => 'url_shortener_management.create'],
                    ],
                ],
            ],
        ],
    ];

    // Permission actions
    public const ACTION_VIEW = 'view';
    public const ACTION_CREATE = 'create';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';
    public const ACTION_EXPORT = 'export';
    public const ACTION_VIEW_REPORT = 'view_report';
    public const ACTION_EXPORT_REPORT = 'export_report';
    public const ACTION_ASSIGN_PERMISSION = 'assign_permission';
    public const ACTION_APPROVE = 'approve';
    public const ACTION_REJECT = 'reject';

    /**
     * Map module names to their display names.
     */
    private static array $moduleLabels = [
        self::MODULE_ROLE_MANAGEMENT => 'role_management',
        self::MODULE_USER_MANAGEMENT => 'user_management',
        self::MODULE_SECTION_MANAGEMENT => 'section_management',
        self::MODULE_PRODUCT_MANAGEMENT => 'product_management',
        self::MODULE_ENTERPRISE_MANAGEMENT => 'enterprise_management',
        self::MODULE_POST_MANAGEMENT => 'post_management',
        self::MODULE_REDIRECT_POST_MANAGEMENT => 'redirect_post_management',
        self::MODULE_REVIEW_POST_MANAGEMENT => 'review_post_management',
        self::MODULE_SCHOOL_POST_MANAGEMENT => 'school_post_management',
        self::MODULE_QUOTE_POST_MANAGEMENT => 'quote_post_management',
        self::MODULE_CATEGORY_MANAGEMENT => 'category_management',
        self::MODULE_EVENT_MANAGEMENT => 'event_management',
        self::MODULE_AWARD_MANAGEMENT => 'award_management',
        self::MODULE_STANDARD_MANAGEMENT => 'standard_management',
        self::MODULE_URL_SHORTENER_MANAGEMENT => 'url_shortener_management',
        self::MODULE_PREGNANCY_WEEK_MANAGEMENT => 'pregnancy_week_management',
    ];

    /**
     * Get all module names.
     *
     * @return array
     */
    public static function getModules(): array
    {
        $modules = [];
        foreach (self::$moduleConfig as $category) {
            $modules = array_merge($modules, array_keys($category['modules']));
        }
        return array_unique($modules);
    }

    /**
     * Get all modules with their categories, labels, icons, and children.
     *
     * @return array
     */
    public static function getModulesWithCategories(): array
    {
        $categories = [];
        foreach (self::$moduleConfig as $categoryKey => $category) {
            $modules = [];
            foreach ($category['modules'] as $moduleKey => $module) {
                $routeName = str_replace('_management', '', $moduleKey);
                $routeName = str_replace('_', '-', $routeName); // Thêm replace để nhất quán với route name (dấu gạch ngang)
                $modules[] = [
                    'name' => $routeName,
                    'label' => $module['label'],
                    'icon' => $module['icon'],
                    'children' => $module['children'] ?? [],
                    'full_name' => $moduleKey,
                ];
            }
            $categories[] = [
                'key' => $categoryKey,
                'label' => $category['label'],
                'modules' => $modules,
            ];
        }
        return $categories;
    }

    /**
     * Get all modules with their display names and route-compatible names.
     *
     * @return array
     */
    public static function getModulesWithLabels(): array
    {
        $modules = [];
        foreach (self::getModules() as $module) {
            $routeName = str_replace('_management', '', $module);
            $routeName = str_replace('_', '-', $routeName); // Nhất quán với route
            $modules[] = [
                'name' => $routeName,
                'label' => self::$moduleLabels[$module] ?? ucfirst(str_replace('_', ' ', $routeName)),
            ];
        }
        return $modules;
    }

    /**
     * Get authorized modules for a user based on their permissions.
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    public static function getAuthorizedModules(?\App\Models\User $user): array
    {
        if (!$user) {
            return [];
        }

        $categories = [];
        foreach (self::getModulesWithCategories() as $category) {
            $authorizedModules = [];
            foreach ($category['modules'] as $module) {
                if ($user->hasPermission($module['full_name'], self::ACTION_VIEW)) {
                    $authorizedModules[] = $module;
                }
            }
            if (!empty($authorizedModules)) {
                $categories[] = [
                    'key' => $category['key'],
                    'label' => $category['label'],
                    'modules' => $authorizedModules,
                ];
            }
        }
        return $categories;
    }

    /**
     * Get display name for a specific module.
     *
     * @param string $module
     * @return string
     */
    public static function getModuleLabel(string $module): string
    {
        foreach (self::$moduleConfig as $category) {
            if (isset($category['modules'][$module])) {
                return $category['modules'][$module]['label'];
            }
        }
        return ucfirst(str_replace('_', ' ', $module));
    }

    /**
     * Check if a module name is valid.
     *
     * @param string $module
     * @return bool
     */
    public static function isValidModule(string $module): bool
    {
        foreach (self::$moduleConfig as $category) {
            if (array_key_exists($module, $category['modules'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all actions.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_VIEW,
            self::ACTION_CREATE,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
            self::ACTION_EXPORT,
            self::ACTION_VIEW_REPORT,
            self::ACTION_EXPORT_REPORT,
            self::ACTION_ASSIGN_PERMISSION,
            self::ACTION_APPROVE,
            self::ACTION_REJECT,
        ];
    }
}