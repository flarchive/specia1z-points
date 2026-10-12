import Extend from 'flarum/common/extenders';
import app from 'flarum/admin/app';

export { default as extend } from './extend';

app.initializers.add('acme-points', () => {});
