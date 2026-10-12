import Extend from 'flarum/common/extenders';
import app from 'flarum/admin/app';
import UserListPage from 'flarum/admin/components/UserListPage';
import Button from 'flarum/common/components/Button';
import { extend } from 'flarum/common/extend';
import AdjustPointsModal from './components/AdjustPointsModal';
import { registerPointsStyleSetting } from './components/PointsStyleSetting';

const label = (key: string) => app.translator.trans(`acme-points.admin.settings.${key}`);
const help = (key: string) => app.translator.trans(`acme-points.admin.settings.${key}_help`);

registerPointsStyleSetting();

export default [
  new Extend.Admin()
    .setting(() => ({
      setting: 'acme-points.currency_name',
      label: label('currency_name'),
      help: help('currency_name'),
      type: 'text',
    }))
    .setting(() => ({
      setting: 'acme-points.currency_icon',
      label: label('currency_icon'),
      help: help('currency_icon'),
      type: 'text',
      placeholder: 'fas fa-coins',
    }))
    .setting(() => ({
      setting: 'acme-points.currency_style',
      label: label('currency_style'),
      help: help('currency_style'),
      type: 'acme-points.style',
      default: 'gold',
    }))
    .setting(() => ({
      setting: 'acme-points.registration_reward',
      label: label('registration_reward'),
      help: help('registration_reward'),
      type: 'number',
      min: 0,
    }))
    .setting(() => ({
      setting: 'acme-points.discussion_reward',
      label: label('discussion_reward'),
      help: help('discussion_reward'),
      type: 'number',
      min: 0,
    }))
    .setting(() => ({
      setting: 'acme-points.reply_reward',
      label: label('reply_reward'),
      help: help('reply_reward'),
      type: 'number',
      min: 0,
    }))
    .setting(() => ({
      setting: 'acme-points.best_answer_reward',
      label: label('best_answer_reward'),
      help: help('best_answer_reward'),
      type: 'number',
      min: 0,
    }))
    .setting(() => ({
      setting: 'acme-points.like_reward',
      label: label('like_reward'),
      help: help('like_reward'),
      type: 'number',
      min: 0,
    }))
    .setting(() => ({
      setting: 'acme-points.suspend_penalty',
      label: label('suspend_penalty'),
      help: help('suspend_penalty'),
      type: 'number',
      min: 0,
    }))
    .setting(() => ({
      setting: 'acme-points.delete_reward',
      label: label('delete_reward'),
      help: help('delete_reward'),
      type: 'boolean',
    }))
    .permission(
      () => ({
        icon: 'fas fa-plus-circle',
        label: app.translator.trans('acme-points.admin.permissions.add_points'),
        permission: 'points.add',
      }),
      'moderate',
      90
    )
    .permission(
      () => ({
        icon: 'fas fa-minus-circle',
        label: app.translator.trans('acme-points.admin.permissions.subtract_points'),
        permission: 'points.subtract',
      }),
      'moderate',
      89
    )
    .permission(
      () => ({
        icon: 'fas fa-sliders-h',
        label: app.translator.trans('acme-points.admin.permissions.adjust_points'),
        permission: 'points.adjust',
      }),
      'moderate',
      88
    ),
];

extend(UserListPage.prototype, 'userActionItems', (items, user) => {
  items.add(
    'adjustPoints',
    <Button icon="fas fa-coins" onclick={() => app.modal.show(AdjustPointsModal, { user })}>
      {app.translator.trans('acme-points.admin.adjust_points.button')}
    </Button>,
    0
  );
});
