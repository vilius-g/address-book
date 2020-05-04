import React from 'react';
import {Route} from 'react-router-dom';
import {Create, List, Show, Update} from '../components/sharedcontact/';

export default [
    <Route path="/shared_contacts/create" component={Create} exact key="create"/>,
    <Route path="/shared_contacts/edit/:id" component={Update} exact key="update"/>,
    <Route path="/shared_contacts/show/:id" component={Show} exact key="show"/>,
    <Route path="/shared_contacts/" component={List} exact strict key="list"/>,
    <Route path="/shared_contacts/:page" component={List} exact strict key="page"/>
];
