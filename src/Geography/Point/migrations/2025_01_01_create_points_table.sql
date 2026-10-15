-- Миграция: Создание таблицы населённых пунктов
-- Таблица: points

CREATE TABLE IF NOT EXISTS points (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    district_id UUID NOT NULL REFERENCES districts(id) ON DELETE RESTRICT,
    type VARCHAR(20) NOT NULL CHECK (type IN ('city', 'town', 'village', 'settlement')),
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Индексы
CREATE INDEX IF NOT EXISTS idx_points_district_id ON points(district_id);
CREATE INDEX IF NOT EXISTS idx_points_type ON points(type);
CREATE INDEX IF NOT EXISTS idx_points_name ON points(name);
CREATE INDEX IF NOT EXISTS idx_points_code ON points(code);

-- Триггер для автоматического обновления updated_at
CREATE OR REPLACE FUNCTION update_points_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_points_updated_at
    BEFORE UPDATE ON points
    FOR EACH ROW
    EXECUTE FUNCTION update_points_updated_at();

-- Комментарии
COMMENT ON TABLE points IS 'Справочник населённых пунктов';
COMMENT ON COLUMN points.id IS 'Уникальный идентификатор';
COMMENT ON COLUMN points.name IS 'Название населённого пункта';
COMMENT ON COLUMN points.code IS 'Уникальный код населённого пункта';
COMMENT ON COLUMN points.district_id IS 'Ссылка на район (districts.id)';
COMMENT ON COLUMN points.type IS 'Тип: city (город), town (пгт), village (село), settlement (нас. пункт)';
COMMENT ON COLUMN points.description IS 'Описание';
COMMENT ON COLUMN points.created_at IS 'Дата создания';
COMMENT ON COLUMN points.updated_at IS 'Дата обновления';
