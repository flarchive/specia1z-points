import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import FormGroup from 'flarum/common/components/FormGroup';
import { extend } from 'flarum/common/extend';
import type { IFormGroupAttrs } from 'flarum/common/components/FormGroup';
import type Stream from 'flarum/common/utils/Stream';
import { normalizePointsStyle, pointsStyles, PointsStyle } from '../../common/pointsStyles';

interface PointsStyleSettingAttrs extends IFormGroupAttrs {
  settingValue: Stream<string>;
}

export default class PointsStyleSetting extends Component<PointsStyleSettingAttrs> {
  view() {
    const selected = normalizePointsStyle(this.attrs.settingValue());
    const currencyName = this.attrs.getSetting?.('acme-points.currency_name')?.() || '积分';
    const currencyIcon = this.attrs.getSetting?.('acme-points.currency_icon')?.() || 'fas fa-coins';

    return (
      <div className="Form-group PointsStyleSetting">
        <label>{this.attrs.label}</label>
        {this.attrs.help && <p className="helpText">{this.attrs.help}</p>}
        <div className="PointsStyleSetting-options" role="radiogroup" aria-label={this.attrs.label as string}>
          {pointsStyles.map((preset) => this.option(preset.id, selected, currencyName, currencyIcon))}
        </div>
      </div>
    );
  }

  private option(style: PointsStyle, selected: PointsStyle, currencyName: string, currencyIcon: string) {
    const preset = pointsStyles.find((item) => item.id === style)!;
    const isSelected = selected === style;

    return (
      <button
        type="button"
        className={`PointsStyleOption PointsStyleOption--${style}${isSelected ? ' is-selected' : ''}`}
        role="radio"
        aria-checked={isSelected}
        onclick={() => this.attrs.settingValue(style)}
      >
        <span className="PointsStyleOption-header">
          <span className="PointsStyleOption-name">
            {app.translator.trans(`acme-points.admin.settings.styles.${preset.label}`)}
          </span>
          {isSelected && <i className="fas fa-check" aria-hidden="true" />}
        </span>
        <span className="PointsStyleOption-preview">
          <span className={`PointsBadge PointsBadge--${style}`}>
            <span className="PointsBadge-icon" aria-hidden="true">
              <i className={currencyIcon} />
            </span>
            <span className="PointsBadge-value">125 {currencyName}</span>
          </span>
        </span>
        <span className="PointsStyleOption-description">
          {app.translator.trans(`acme-points.admin.settings.styles.${preset.description}`)}
        </span>
      </button>
    );
  }
}

export function registerPointsStyleSetting() {
  extend(FormGroup.prototype, 'customFieldComponents', (items) => {
    items.add('acme-points.style', (attrs: IFormGroupAttrs) => (
      <PointsStyleSetting {...attrs} settingValue={attrs.stream as Stream<string>} />
    ));
  });
}
