import React from 'react';
import {Route} from 'react-router-dom';
import {Create, List, Show, Update} from '../components/contact/';

export default [
    <Route path="/contacts/create" component={Create} exact key="create"/>,
    <Route path="/contacts/edit/:id" component={Update} exact key="update"/>,
    <Route path="/contacts/show/:id" component={Show} exact key="show"/>,
    <Route path="/contacts/" component={List} exact strict key="list"/>,
    <Route path="/contacts/:page" component={List} exact strict key="page"/>
];
