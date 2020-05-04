/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

import React from 'react';
import ReactDOM from 'react-dom';
import {applyMiddleware, combineReducers, createStore} from 'redux';
import {Provider} from 'react-redux';
import thunk from 'redux-thunk';
import {reducer as form} from 'redux-form';
import {Route, Switch} from 'react-router-dom';
import {createBrowserHistory} from 'history';
import {ConnectedRouter, connectRouter, routerMiddleware} from 'connected-react-router';
import 'bootstrap/dist/css/bootstrap.css';
import 'font-awesome/css/font-awesome.css';
// import * as serviceWorker from './serviceWorker';
// Import your reducers and routes here
import contact from '../src/reducers/contact/';
import contactRoutes from '../src/routes/contact';
import sharedcontact from '../src/reducers/sharedcontact/';
import sharedcontactRoutes from '../src/routes/sharedcontact';
import Login from "../src/components/login";
// import Welcome from './Welcome';

const history = createBrowserHistory();
const store = createStore(
    combineReducers({
        router: connectRouter(history),
        form,
        /* Add your reducers here */
        contact,
        sharedcontact
    }),
    applyMiddleware(routerMiddleware(history), thunk)
);

ReactDOM.render(
    <Provider store={store}>
        <ConnectedRouter history={history}>
            <Switch>
                <Route path="/" component={Login} strict={true} exact={true}/>
                {/* Add your routes here */}
                {contactRoutes}
                {sharedcontactRoutes}
                <Route render={() => <h1>Not Found</h1>}/>
            </Switch>
        </ConnectedRouter>
    </Provider>,
    document.getElementById('root')
);

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
// serviceWorker.unregister();
