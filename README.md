# 🏠 CRM для базы недвижимости

> Система управления клиентскими отношениями для агентства недвижимости

## 📋 Описание проекта

CRM-система для управления базой объектов недвижимости, клиентами, сделками и аналитикой. Предназначена для агентств недвижимости, риелторов и управляющих компаний.

## 🎯 Цели проекта

- Централизованное управление базой объектов недвижимости
- Учёт клиентов и их запросов
- Отслеживание сделок на всех этапах
- Аналитика и отчётность
- Управление задачами и напоминаниями

## 🏗️ Архитектура

### Технологический стек

| Категория | Технология |
|-----------|-----------|
| Backend | PHP 8.x |
| Фреймворк | Slim Framework 4 |
| БД | PostgreSQL |
| API | REST (JSON) |
| Документация | OpenAPI 3.0 (Swagger) |

### Структура проекта

```
├── src/
│   ├── Geography/                    # Справочники географии
│   │   ├── MacroRegion/              # Макрорегионы
│   │   │   ├── MacroRegion.php       # Сущность
│   │   │   ├── MacroRegionRepository.php
│   │   │   ├── MacroRegionService.php
│   │   │   ├── MacroRegionController.php
│   │   │   ├── routes.php
│   │   │   └── migrations/
│   │   │
│   │   ├── Region/                   # Регионы
│   │   │   ├── Region.php
│   │   │   ├── RegionRepository.php
│   │   │   ├── RegionService.php
│   │   │   ├── RegionController.php
│   │   │   ├── routes.php
│   │   │   └── migrations/
│   │   │
│   │   ├── District/                 # Районы
│   │   │   ├── District.php
│   │   │   ├── DistrictRepository.php
│   │   │   ├── DistrictService.php
│   │   │   ├── DistrictController.php
│   │   │   ├── routes.php
│   │   │   └── migrations/
│   │   │
│   │   └── Point/                    # Населённые пункты
│   │       ├── Point.php             # Сущность
│   │       ├── PointRepository.php   # Репозиторий (CRUD)
│   │       ├── PointService.php      # Бизнес-логика
│   │       ├── PointController.php   # HTTP контроллер
│   │       ├── routes.php            # Маршруты API
│   │       └── migrations/
│   │           └── 2025_01_01_create_points_table.sql
│   │
│   └── ... (другие модули)
│
├── public/
│   └── documentation/
│       └── api/
│           └── geography.yaml        # OpenAPI документация
│
└── ...
```

## 📂 Справочники

Система использует иерархическую структуру территориальных справочников:

```
MacroRegion (Макрорегион)
    └── Region (Регион)          ← macro_region_id
        └── District (Район)     ← region_id
            └── Point (Населённый пункт) ← district_id
```

### Структура справочников

| Справочник | Таблица | Связь | Описание |
|-----------|---------|-------|----------|
| Макрорегион | `macro_regions` | — | Крупные территориальные объединения |
| Регион | `regions` | `macro_region_id → macro_regions.id` | Субъекты федерации |
| Район | `districts` | `region_id → regions.id` | Районы / городские округа |
| Населённый пункт | `points` | `district_id → districts.id` | Города, посёлки, сёла |

### Типы населённых пунктов (Point)

| Значение | Описание |
|----------|----------|
| `city` | Город |
| `town` | Посёлок городского типа |
| `village` | Село |
| `settlement` | Населённый пункт |

## 📐 Модели данных

### Объект недвижимости (Property)

```typescript
interface Property {
  id: string;
  title: string;
  type: 'apartment' | 'house' | 'commercial' | 'land';
  dealType: 'sale' | 'rent';
  status: 'active' | 'reserved' | 'sold' | 'archived';
  address: Address;
  area: number;           // площадь, м²
  rooms: number;
  floor?: number;
  totalFloors?: number;
  price: number;
  pricePerMeter: number;
  description: string;
  photos: string[];
  features: string[];     // особенности
  manager: string;        // ID менеджера
  createdAt: Date;
  updatedAt: Date;
}
```

### Клиент (Client)

```typescript
interface Client {
  id: string;
  firstName: string;
  lastName: string;
  phone: string;
  email: string;
  source: string;         // источник обращения
  type: 'buyer' | 'seller' | 'tenant' | 'landlord';
  budget?: PriceRange;
  preferences: PropertyPreferences;
  notes: string;
  assignedManager: string;
  interactions: Interaction[];
  createdAt: Date;
}
```

### Сделка (Deal)

```typescript
interface Deal {
  id: string;
  propertyId: string;
  clientId: string;
  stage: DealStage;
  amount: number;
  commission: number;
  manager: string;
  documents: Document[];
  history: DealHistory[];
  createdAt: Date;
  closedAt?: Date;
}

type DealStage = 
  | 'new'
  | 'viewing_scheduled'
  | 'offer_made'
  | 'negotiation'
  | 'documents'
  | 'closed_won'
  | 'closed_lost';
```

## 📄 Страницы приложения

| Маршрут | Страница | Описание |
|---------|----------|----------|
| `/` | Dashboard | Главный дашборд с ключевыми метриками |
| `/properties` | Объекты | Каталог недвижимости |
| `/properties/:id` | Карточка объекта | Детальная информация об объекте |
| `/clients` | Клиенты | Список клиентов |
| `/clients/:id` | Карточка клиента | Профиль клиента |
| `/deals` | Сделки | Канбан-доска сделок |
| `/deals/:id` | Детали сделки | Информация о сделке |
| `/tasks` | Задачи | Список задач и напоминаний |
| `/analytics` | Аналитика | Графики и отчёты |
| `/settings` | Настройки | Настройки системы |

## 🎨 Дизайн-система

### Цветовая палитра

- **Primary** — основной цвет бренда
- **Success** — успешные действия, закрытые сделки
- **Warning** — предупреждения, ожидающие действия
- **Danger** — ошибки, просроченные задачи
- **Neutral** — фоны, текст, границы

### Компоненты

- Карточки (Cards)
- Таблицы с сортировкой и фильтрацией
- Формы с валидацией
- Модальные окна
- Уведомления (Toast)
- Kanban-доска
- Календарь
- Графики

## 🚀 План разработки

### Этап 1 — MVP (текущий)
- [x] Инициализация проекта
- [x] Справочник Макрорегионы (MacroRegion)
- [x] Справочник Регионы (Region)
- [x] Справочник Районы (District)
- [x] Справочник Населённые пункты (Point)
- [x] OpenAPI документация (geography.yaml)
- [ ] CRUD для объектов недвижимости
- [ ] Модуль клиентов

### Этап 2 — Клиенты и сделки
- [ ] Модуль клиентов
- [ ] Канбан-доска сделок
- [ ] Связи клиент ↔ объект ↔ сделка
- [ ] История взаимодействий

### Этап 3 — Аналитика и задачи
- [ ] Дашборд с метриками
- [ ] Графики и отчёты
- [ ] Система задач
- [ ] Уведомления

### Этап 4 — Улучшения
- [ ] Загрузка фотографий
- [ ] Экспорт данных
- [ ] Шаблоны документов
- [ ] Мобильная адаптация
- [ ] PWA

## 📁 Структура PHP проекта

```
src/
├── Geography/              # Модуль географии
│   ├── MacroRegion/        # Макрорегионы
│   ├── Region/             # Регионы
│   ├── District/           # Районы
│   └── Point/              # Населённые пункты
│       ├── Point.php
│       ├── PointRepository.php
│       ├── PointService.php
│       ├── PointController.php
│       ├── routes.php
│       └── migrations/
│
├── Property/               # Объекты недвижимости
├── Client/                 # Клиенты
├── Deal/                   # Сделки
└── ...

public/
├── index.php               # Точка входа
└── documentation/
    └── api/
        └── geography.yaml  # OpenAPI спецификация
```

## 🔧 Установка и запуск

```bash
# Установка зависимостей Composer
composer install

# Настройка базы данных
# 1. Создать базу данных PostgreSQL
# 2. Выполнить миграции из src/*/migrations/

# Запуск встроенного PHP сервера
php -S localhost:8000 -t public

# API будет доступен по адресу:
# http://localhost:8000/api/geography/points

# OpenAPI документация:
# public/documentation/api/geography.yaml
```

## 📝 Лицензия

Проект в разработке.

---

*CRM для базы недвижимости — управляйте объектами, клиентами и сделками эффективно* 🏡
