import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Model from 'flarum/common/Model';
import PostUser from 'flarum/forum/components/PostUser';
import UserCard from 'flarum/forum/components/UserCard';
import { normalizePointsStyle } from '../common/pointsStyles';

export { default as extend } from './extend';

app.initializers.add('acme-points', () => {
  extend(PostUser.prototype, 'userViewItems', (items, user) => {
    items.add(
      'points',
       <span className={`PointsBadge PointsBadge--${pointsStyle()} PostUser-points`} title={formatPoints(user)}>
        <span className="PointsBadge-icon" aria-hidden="true">
          <i className={pointsIcon()} />
        </span>
        <span className="PointsBadge-value">{formatPoints(user)}</span>
      </span>,
      80
    );
  });

  extend(UserCard.prototype, 'infoItems', function (items) {
    const user = this.attrs.user;

    items.add(
      'points',
       <span className={`PointsBadge PointsBadge--${pointsStyle()} UserCard-points`} title={formatPoints(user)}>
        <span className="PointsBadge-icon" aria-hidden="true">
          <i className={pointsIcon()} />
        </span>
        <span className="PointsBadge-value">{formatPoints(user)}</span>
      </span>,
      80
    );
  });
});

function formatPoints(user: Model) {
  const count = Number(user.attribute('points') || 0);
  const name = app.forum.attribute('pointsName') || 'Points';

  return app.translator.trans('acme-points.forum.points', { count, name });
}

function pointsIcon() {
  return app.forum.attribute('pointsIcon') || 'fas fa-coins';
}

function pointsStyle() {
  return normalizePointsStyle(app.forum.attribute('pointsStyle'));
}
