import app from 'flarum/admin/app';
import FormModal, { IFormModalAttrs } from 'flarum/common/components/FormModal';
import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';
import type User from 'flarum/common/models/User';
import type Mithril from 'mithril';

interface AdjustPointsModalAttrs extends IFormModalAttrs {
  user: User;
}

export default class AdjustPointsModal extends FormModal<AdjustPointsModalAttrs> {
  amount = Stream('');
  type = Stream('add');

  className() {
    return 'AdjustPointsModal Modal--small';
  }

  title() {
    return app.translator.trans('acme-points.admin.adjust_points.title', { username: this.attrs.user.displayName() });
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="Form-group">
          <label>{app.translator.trans('acme-points.admin.adjust_points.type_label')}</label>
          <select className="FormControl" bidi={this.type}>
            <option value="add">{app.translator.trans('acme-points.admin.adjust_points.add')}</option>
            <option value="subtract">{app.translator.trans('acme-points.admin.adjust_points.subtract')}</option>
            <option value="set">{app.translator.trans('acme-points.admin.adjust_points.set')}</option>
          </select>
        </div>
        <div className="Form-group">
          <label>{app.translator.trans('acme-points.admin.adjust_points.amount_label')}</label>
          <input className="FormControl" type="number" min="0" step="1" bidi={this.amount} required />
        </div>
        <div className="Form-group Form-controls">
          <Button className="Button Button--primary" type="submit" loading={this.loading}>
            {app.translator.trans('acme-points.admin.adjust_points.submit')}
          </Button>
        </div>
      </div>
    );
  }

  onsubmit(event: SubmitEvent) {
    event.preventDefault();
    this.loading = true;

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/points/adjust',
        body: {
          userId: this.attrs.user.id(),
          type: this.type(),
          amount: Number(this.amount()),
        },
      })
      .then((response: any) => {
        this.attrs.user.pushAttributes({ points: response.data.attributes.balance });
        this.hide();
      })
      .catch(this.onerror.bind(this))
      .finally(() => {
        this.loading = false;
        m.redraw();
      });
  }
}
