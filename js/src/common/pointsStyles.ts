export type PointsStyle = 'gold' | 'emerald' | 'ocean' | 'violet' | 'graphite';

export const pointsStyles: { id: PointsStyle; label: string; description: string }[] = [
  { id: 'gold', label: 'gold', description: 'gold_help' },
  { id: 'emerald', label: 'emerald', description: 'emerald_help' },
  { id: 'ocean', label: 'ocean', description: 'ocean_help' },
  { id: 'violet', label: 'violet', description: 'violet_help' },
  { id: 'graphite', label: 'graphite', description: 'graphite_help' },
];

export function normalizePointsStyle(value: unknown): PointsStyle {
  const style = String(value || 'gold') as PointsStyle;

  return pointsStyles.some((preset) => preset.id === style) ? style : 'gold';
}
