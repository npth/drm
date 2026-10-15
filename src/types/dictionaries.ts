// ===== Макрорегион =====
export interface MacroRegion {
  id: string;
  name: string;
  code: string;
  description?: string;
  created_at: string;
  updated_at: string;
}

// ===== Регион =====
export interface Region {
  id: string;
  name: string;
  code: string;
  macro_region_id: string;
  description?: string;
  created_at: string;
  updated_at: string;
}

// ===== Район =====
export interface District {
  id: string;
  name: string;
  code: string;
  region_id: string;
  description?: string;
  created_at: string;
  updated_at: string;
}

// ===== Населённый пункт =====
export interface Point {
  id: string;
  name: string;
  code: string;
  district_id: string;
  type: 'city' | 'town' | 'village' | 'settlement';
  description?: string;
  created_at: string;
  updated_at: string;
}

// ===== Типы для выпадающих списков =====
export type PointType = Point['type'];

export const POINT_TYPE_LABELS: Record<PointType, string> = {
  city: 'Город',
  town: 'Посёлок городского типа',
  village: 'Село',
  settlement: 'Населённый пункт',
};
