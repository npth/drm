import { useState, useMemo } from 'react';
import { MapPin, Building2, Map, Globe, ChevronRight, Layers } from 'lucide-react';
import { DictionaryTable } from './DictionaryTable';
import type { DictionaryColumn } from './DictionaryTable';
import type { MacroRegion, Region, District, Point, PointType } from '../types/dictionaries';
import { POINT_TYPE_LABELS } from '../types/dictionaries';
import { macroRegions, regions, districts, points } from '../data/dictionaries';

type TabKey = 'macro_regions' | 'regions' | 'districts' | 'points';

const TABS: { key: TabKey; label: string; icon: React.ReactNode }[] = [
  { key: 'macro_regions', label: 'Макрорегионы', icon: <Globe className="w-4 h-4" /> },
  { key: 'regions', label: 'Регионы', icon: <Map className="w-4 h-4" /> },
  { key: 'districts', label: 'Районы', icon: <Building2 className="w-4 h-4" /> },
  { key: 'points', label: 'Населённые пункты', icon: <MapPin className="w-4 h-4" /> },
];

export function DictionariesPage() {
  const [activeTab, setActiveTab] = useState<TabKey>('macro_regions');
  const [selectedMacroRegionId, setSelectedMacroRegionId] = useState<string | null>(null);
  const [selectedRegionId, setSelectedRegionId] = useState<string | null>(null);
  const [selectedDistrictId, setSelectedDistrictId] = useState<string | null>(null);

  // Фильтрация регионов по макрорегиону
  const filteredRegions = useMemo(() => {
    if (!selectedMacroRegionId) return regions;
    return regions.filter((r) => r.macro_region_id === selectedMacroRegionId);
  }, [selectedMacroRegionId]);

  // Фильтрация районов по региону
  const filteredDistricts = useMemo(() => {
    if (!selectedRegionId) return districts;
    return districts.filter((d) => d.region_id === selectedRegionId);
  }, [selectedRegionId]);

  // Фильтрация населённых пунктов по району
  const filteredPoints = useMemo(() => {
    if (!selectedDistrictId) return points;
    return points.filter((p) => p.district_id === selectedDistrictId);
  }, [selectedDistrictId]);

  // Получение названия макрорегиона по ID
  const getMacroRegionName = (id: string) => macroRegions.find((m) => m.id === id)?.name ?? '—';
  const getRegionName = (id: string) => regions.find((r) => r.id === id)?.name ?? '—';
  const getDistrictName = (id: string) => districts.find((d) => d.id === id)?.name ?? '—';

  // Колонки для таблиц
  const macroRegionColumns: DictionaryColumn<MacroRegion>[] = [
    { key: 'code', label: 'Код' },
    { key: 'name', label: 'Название' },
    {
      key: 'id',
      label: 'Регионов',
      render: (_: string, item: MacroRegion) =>
        regions.filter((r) => r.macro_region_id === item.id).length,
    },
    { key: 'description', label: 'Описание' },
  ];

  const regionColumns: DictionaryColumn<Region>[] = [
    { key: 'code', label: 'Код' },
    { key: 'name', label: 'Название' },
    {
      key: 'macro_region_id',
      label: 'Макрорегион',
      render: (val: string) => getMacroRegionName(val),
    },
    {
      key: 'id',
      label: 'Районов',
      render: (_: string, item: Region) =>
        districts.filter((d) => d.region_id === item.id).length,
    },
    { key: 'description', label: 'Описание' },
  ];

  const districtColumns: DictionaryColumn<District>[] = [
    { key: 'code', label: 'Код' },
    { key: 'name', label: 'Название' },
    {
      key: 'region_id',
      label: 'Регион',
      render: (val: string) => getRegionName(val),
    },
    {
      key: 'id',
      label: 'Нас. пунктов',
      render: (_: string, item: District) =>
        points.filter((p) => p.district_id === item.id).length,
    },
    { key: 'description', label: 'Описание' },
  ];

  const pointColumns: DictionaryColumn<Point>[] = [
    { key: 'code', label: 'Код' },
    { key: 'name', label: 'Название' },
    {
      key: 'type',
      label: 'Тип',
      render: (val: PointType) => (
        <span
          className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${
            val === 'city'
              ? 'bg-blue-100 text-blue-700'
              : val === 'town'
              ? 'bg-green-100 text-green-700'
              : val === 'village'
              ? 'bg-yellow-100 text-yellow-700'
              : 'bg-gray-100 text-gray-700'
          }`}
        >
          {POINT_TYPE_LABELS[val]}
        </span>
      ),
    },
    {
      key: 'district_id',
      label: 'Район',
      render: (val: string) => getDistrictName(val),
    },
    { key: 'description', label: 'Описание' },
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'macro_regions':
        return (
          <DictionaryTable
            title="Макрорегионы"
            icon={<Globe className="w-5 h-5" />}
            columns={macroRegionColumns}
            data={macroRegions}
            onAdd={() => alert('Добавление макрорегиона')}
            onEdit={(item) => alert(`Редактирование: ${item.name}`)}
            onDelete={(item) => alert(`Удаление: ${item.name}`)}
          />
        );

      case 'regions':
        return (
          <div className="space-y-4">
            {/* Фильтр по макрорегиону */}
            <div className="flex items-center gap-2 flex-wrap">
              <span className="text-sm text-gray-500">Фильтр по макрорегиону:</span>
              <button
                onClick={() => setSelectedMacroRegionId(null)}
                className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                  !selectedMacroRegionId
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                }`}
              >
                Все
              </button>
              {macroRegions.map((mr) => (
                <button
                  key={mr.id}
                  onClick={() => setSelectedMacroRegionId(mr.id)}
                  className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                    selectedMacroRegionId === mr.id
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {mr.name}
                </button>
              ))}
            </div>
            <DictionaryTable
              title="Регионы"
              icon={<Map className="w-5 h-5" />}
              columns={regionColumns}
              data={filteredRegions}
              onAdd={() => alert('Добавление региона')}
              onEdit={(item) => alert(`Редактирование: ${item.name}`)}
              onDelete={(item) => alert(`Удаление: ${item.name}`)}
            />
          </div>
        );

      case 'districts':
        return (
          <div className="space-y-4">
            {/* Фильтр по региону */}
            <div className="flex items-center gap-2 flex-wrap">
              <span className="text-sm text-gray-500">Фильтр по региону:</span>
              <button
                onClick={() => setSelectedRegionId(null)}
                className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                  !selectedRegionId
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                }`}
              >
                Все
              </button>
              {regions.map((r) => (
                <button
                  key={r.id}
                  onClick={() => setSelectedRegionId(r.id)}
                  className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                    selectedRegionId === r.id
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {r.name}
                </button>
              ))}
            </div>
            <DictionaryTable
              title="Районы"
              icon={<Building2 className="w-5 h-5" />}
              columns={districtColumns}
              data={filteredDistricts}
              onAdd={() => alert('Добавление района')}
              onEdit={(item) => alert(`Редактирование: ${item.name}`)}
              onDelete={(item) => alert(`Удаление: ${item.name}`)}
            />
          </div>
        );

      case 'points':
        return (
          <div className="space-y-4">
            {/* Фильтр по району */}
            <div className="flex items-center gap-2 flex-wrap">
              <span className="text-sm text-gray-500">Фильтр по району:</span>
              <button
                onClick={() => setSelectedDistrictId(null)}
                className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                  !selectedDistrictId
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                }`}
              >
                Все
              </button>
              {districts.map((d) => (
                <button
                  key={d.id}
                  onClick={() => setSelectedDistrictId(d.id)}
                  className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                    selectedDistrictId === d.id
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  }`}
                >
                  {d.name}
                </button>
              ))}
            </div>
            <DictionaryTable
              title="Населённые пункты"
              icon={<MapPin className="w-5 h-5" />}
              columns={pointColumns}
              data={filteredPoints}
              onAdd={() => alert('Добавление населённого пункта')}
              onEdit={(item) => alert(`Редактирование: ${item.name}`)}
              onDelete={(item) => alert(`Удаление: ${item.name}`)}
            />
          </div>
        );
    }
  };

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Header */}
      <header className="bg-white border-b border-gray-200 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 py-4">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <Layers className="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h1 className="text-xl font-bold text-gray-800">Справочники</h1>
              <p className="text-sm text-gray-500">
                Территориальная иерархия: Макрорегион → Регион → Район → Населённый пункт
              </p>
            </div>
          </div>
        </div>
      </header>

      <div className="max-w-7xl mx-auto px-4 py-6">
        {/* Breadcrumb иерархия */}
        <div className="flex items-center gap-2 text-sm text-gray-500 mb-4 bg-white rounded-lg px-4 py-2 border border-gray-200">
          <Globe className="w-4 h-4" />
          <span>Макрорегион</span>
          <ChevronRight className="w-4 h-4" />
          <Map className="w-4 h-4" />
          <span>Регион</span>
          <ChevronRight className="w-4 h-4" />
          <Building2 className="w-4 h-4" />
          <span>Район</span>
          <ChevronRight className="w-4 h-4" />
          <MapPin className="w-4 h-4" />
          <span>Населённый пункт</span>
        </div>

        {/* Tabs */}
        <div className="flex gap-1 mb-6 bg-white rounded-xl p-1 border border-gray-200 shadow-sm">
          {TABS.map((tab) => (
            <button
              key={tab.key}
              onClick={() => setActiveTab(tab.key)}
              className={`flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all ${
                activeTab === tab.key
                  ? 'bg-blue-600 text-white shadow-sm'
                  : 'text-gray-600 hover:bg-gray-100'
              }`}
            >
              {tab.icon}
              {tab.label}
            </button>
          ))}
        </div>

        {/* Content */}
        {renderContent()}

        {/* Схема связей */}
        <div className="mt-8 bg-white rounded-xl border border-gray-200 p-6">
          <h3 className="font-semibold text-gray-800 mb-4">Схема связей справочников</h3>
          <div className="flex items-center justify-center gap-2 flex-wrap">
            <div className="px-4 py-2 bg-purple-100 border border-purple-200 rounded-lg text-sm font-medium text-purple-700">
              MacroRegion
            </div>
            <div className="text-gray-400 text-xs">
              <div>macro_region_id</div>
              <div className="flex items-center">──────→</div>
            </div>
            <div className="px-4 py-2 bg-blue-100 border border-blue-200 rounded-lg text-sm font-medium text-blue-700">
              Region
            </div>
            <div className="text-gray-400 text-xs">
              <div>region_id</div>
              <div className="flex items-center">──────→</div>
            </div>
            <div className="px-4 py-2 bg-green-100 border border-green-200 rounded-lg text-sm font-medium text-green-700">
              District
            </div>
            <div className="text-gray-400 text-xs">
              <div>district_id</div>
              <div className="flex items-center">──────→</div>
            </div>
            <div className="px-4 py-2 bg-orange-100 border border-orange-200 rounded-lg text-sm font-medium text-orange-700">
              Point
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
